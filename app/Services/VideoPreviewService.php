<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoPreviewService
{
    /**
     * Generate a thumbnail/preview video from the original video
     * Creates a smaller, lower quality version for preview purposes
     *
     * @param string $videoPath Original video path in storage
     * @return string|null Preview video path or null if generation fails
     */
    public function generatePreview(string $videoPath): ?string
    {
        try {
            // Resolve FFmpeg binary path
            $ffmpeg = $this->getFfmpegBinary();
            if (!$ffmpeg) {
                Log::warning('FFmpeg not available, skipping video preview generation');
                return null;
            }

            $fullPath = Storage::disk('public')->path($videoPath);
            
            // Generate preview path
            $pathInfo = pathinfo($videoPath);
            $previewPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_preview.' . $pathInfo['extension'];
            $previewFullPath = Storage::disk('public')->path($previewPath);

            // Ensure directory exists
            $previewDir = dirname($previewFullPath);
            if (!is_dir($previewDir)) {
                mkdir($previewDir, 0755, true);
            }

            // FFmpeg command to create a smaller, lower quality preview
            // - Scale to max width of 400px (maintains aspect ratio)
            // - Lower bitrate (500k for video)
            // - Lower audio bitrate (64k)
            // - Fast encoding preset
            $process = new Process([
                $ffmpeg,
                '-i', $fullPath,
                '-vf', 'scale=400:-2',  // Scale to 400px width, maintain aspect ratio
                '-b:v', '500k',          // Video bitrate 500 kbps
                '-b:a', '64k',           // Audio bitrate 64 kbps
                '-preset', 'fast',       // Fast encoding
                '-movflags', '+faststart', // Enable streaming
                '-y',                    // Overwrite output file
                $previewFullPath
            ]);

            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('FFmpeg preview generation failed', [
                    'error' => $process->getErrorOutput(),
                    'video_path' => $videoPath
                ]);
                return null;
            }

            return $previewPath;

        } catch (\Exception $e) {
            Log::error('Video preview generation failed', [
                'error' => $e->getMessage(),
                'video_path' => $videoPath
            ]);
            return null;
        }
    }

    /**
     * Generate a poster/thumbnail image from the video
     *
     * @param string $videoPath Original video path in storage
     * @return string|null Poster image path or null if generation fails
     */
    public function generatePoster(string $videoPath): ?string
    {
        try {
            $ffmpeg = $this->getFfmpegBinary();
            if (!$ffmpeg) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($videoPath);
            
            $pathInfo = pathinfo($videoPath);
            $posterPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_poster.jpg';
            $posterFullPath = Storage::disk('public')->path($posterPath);

            // Ensure directory exists
            $posterDir = dirname($posterFullPath);
            if (!is_dir($posterDir)) {
                mkdir($posterDir, 0755, true);
            }

            // Extract frame at 1 second
            $process = new Process([
                $ffmpeg,
                '-i', $fullPath,
                '-ss', '00:00:01',       // Seek to 1 second
                '-vframes', '1',         // Extract 1 frame
                '-vf', 'scale=400:-2',   // Scale to 400px width
                '-q:v', '2',             // High quality JPEG
                '-y',
                $posterFullPath
            ]);

            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Poster generation failed', [
                    'error' => $process->getErrorOutput(),
                    'video_path' => $videoPath
                ]);
                return null;
            }

            return $posterPath;

        } catch (\Exception $e) {
            Log::error('Poster generation failed', [
                'error' => $e->getMessage(),
                'video_path' => $videoPath
            ]);
            return null;
        }
    }

    /**
     * Generate HLS playlist and segments for adaptive playback.
     * Stores files under public disk (e.g. storage/app/public/hls/{uploadId}/playlist.m3u8)
     *
     * @param string $videoPath
     * @param string $outputDirRelative Relative directory under public disk (e.g. "hls/{id}")
     * @return string|null Relative path to playlist (public disk) or null
     */
    public function generateHls(string $videoPath, string $outputDirRelative): ?string
    {
        try {
            $ffmpeg = $this->getFfmpegBinary();
            if (!$ffmpeg) {
                Log::warning('FFmpeg not available, skipping HLS generation');
                return null;
            }

            $fullPath = Storage::disk('public')->path($videoPath);

            $outputDir = Storage::disk('public')->path($outputDirRelative);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $playlistName = 'playlist.m3u8';
            $playlistFull = $outputDir . DIRECTORY_SEPARATOR . $playlistName;
            $segmentPattern = $outputDir . DIRECTORY_SEPARATOR . 'segment_%03d.ts';

            // Example single-bitrate HLS generation (modify for multi-bitrate if needed)
            $process = new Process([
                $ffmpeg,
                '-i', $fullPath,
                '-preset', 'fast',
                '-g', '48',
                '-sc_threshold', '0',
                '-hls_time', '10',
                '-hls_list_size', '0',
                '-hls_segment_filename', $segmentPattern,
                '-y',
                $playlistFull
            ]);

            // Allow longer time for HLS generation
            $process->setTimeout(900); // 15 minutes
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('HLS generation failed', [
                    'error' => $process->getErrorOutput(),
                    'video_path' => $videoPath
                ]);
                return null;
            }

            return rtrim($outputDirRelative, '/') . '/' . $playlistName;
        } catch (\Exception $e) {
            Log::error('HLS generation exception', ['error' => $e->getMessage(), 'video_path' => $videoPath]);
            return null;
        }
    }

    /**
     * Check if FFmpeg is available on the system
     *
     * @return bool
     */
    private function isFfmpegAvailable(): bool
    {
        try {
            $ffmpeg = $this->getFfmpegBinary();
            if (!$ffmpeg) return false;

            $process = new Process([$ffmpeg, '-version']);
            $process->run();
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Attempt to locate an ffmpeg binary.
     * Prefer env('FFMPEG_BINARY'), then common install locations.
     *
     * @return string|null
     */
    private function getFfmpegBinary(): ?string
    {
        $env = env('FFMPEG_BINARY');
        if ($env && is_executable($env)) {
            return $env;
        }

        $candidates = [
            '/opt/homebrew/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            '/usr/bin/ffmpeg',
            '/bin/ffmpeg'
        ];

        foreach ($candidates as $bin) {
            if (is_executable($bin)) {
                return $bin;
            }
        }

        return null;
    }

    /**
     * Delete preview and poster files when video is deleted
     *
     * @param string $videoPath
     * @return void
     */
    public function deletePreviewFiles(string $videoPath): void
    {
        try {
            $pathInfo = pathinfo($videoPath);
            $previewPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_preview.' . $pathInfo['extension'];
            $posterPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_poster.jpg';

            if (Storage::disk('public')->exists($previewPath)) {
                Storage::disk('public')->delete($previewPath);
            }

            if (Storage::disk('public')->exists($posterPath)) {
                Storage::disk('public')->delete($posterPath);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete preview files', [
                'error' => $e->getMessage(),
                'video_path' => $videoPath
            ]);
        }
    }
}
