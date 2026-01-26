<?php

namespace App\Jobs;

use App\Models\DJ;
use App\Services\VideoPreviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateVideoPreview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $djId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $djId)
    {
        $this->djId = $djId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('GenerateVideoPreview job started', ['dj_id' => $this->djId]);

        $dj = DJ::find($this->djId);
        if (! $dj) {
            Log::warning('GenerateVideoPreview job: DJ not found', ['dj_id' => $this->djId]);
            return;
        }

        if (! $dj->video_path) {
            Log::warning('GenerateVideoPreview job: no video_path', ['dj_id' => $this->djId]);
            return;
        }

        $videoService = new VideoPreviewService();
        try {
            $preview = $videoService->generatePreview($dj->video_path);
            $poster = $videoService->generatePoster($dj->video_path);

            $update = [];
            if ($preview) $update['preview_video_path'] = $preview;
            if ($poster) $update['poster_path'] = $poster;

            // Generate HLS into public/hls/{djId}/playlist.m3u8
            $hlsDir = 'hls/' . $dj->id;
            $hlsPlaylist = $videoService->generateHls($dj->video_path, $hlsDir);
            if ($hlsPlaylist) {
                $update['hls_path'] = $hlsPlaylist;
            }

            // Log generated asset paths before updating the model
            Log::info('GenerateVideoPreview generated assets', [
                'dj_id' => $dj->id,
                'preview' => $preview,
                'poster' => $poster,
                'hls_playlist' => $hlsPlaylist,
            ]);

            if (!empty($update)) {
                $dj->update($update);
                Log::info('GenerateVideoPreview updated DJ record', ['dj_id' => $dj->id, 'update' => $update]);
            } else {
                Log::warning('GenerateVideoPreview produced no assets to update', ['dj_id' => $dj->id]);
            }

            Log::info('GenerateVideoPreview job completed', ['dj_id' => $this->djId]);
        } catch (\Throwable $e) {
            Log::error('GenerateVideoPreview job failed', ['dj_id' => $this->djId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
