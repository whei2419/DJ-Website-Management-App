<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    // Receive a single chunk and store it in a temporary folder
    public function receiveChunk(Request $request)
    {
        $uploadId = $request->input('upload_id');
        $index = $request->input('chunk_index');
        $file = $request->file('chunk');

        if (!$uploadId || !$index || !$file) {
            return response()->json(['error' => 'missing parameters'], 422);
        }

        $dir = "tmp/uploads/{$uploadId}";
        Storage::disk('local')->makeDirectory($dir);
        $filename = "part_{$index}";
        Storage::disk('local')->putFileAs($dir, $file, $filename);

        return response()->json(['ok' => true]);
    }

    // Assemble chunks into a single file
    public function completeUpload(Request $request)
    {
        $uploadId = $request->input('upload_id');
        $total = (int) $request->input('total_chunks');
        $original = $request->input('filename', 'upload');

        if (!$uploadId || !$total) {
            return response()->json(['error' => 'missing parameters'], 422);
        }

        $tmpDir = storage_path('app/tmp/uploads/'.$uploadId);
        if (!is_dir($tmpDir)) {
            return response()->json(['error' => 'no such upload'], 404);
        }

        $publicDir = storage_path('app/public/djs/previews');
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $safeName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $original);
        $finalName = $uploadId . '_' . $safeName;
        $finalPath = $publicDir . DIRECTORY_SEPARATOR . $finalName;

        $out = fopen($finalPath, 'wb');

        for ($i = 1; $i <= $total; $i++) {
            $part = $tmpDir . DIRECTORY_SEPARATOR . "part_{$i}";
            if (!file_exists($part)) {
                fclose($out);
                return response()->json(['error' => "missing part {$i}"], 422);
            }
            $in = fopen($part, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
            @unlink($part);
        }

        fclose($out);
        @rmdir($tmpDir);

        // Return storage-relative path so callers can reference it (e.g. save in DB)
        // This path must be relative to the 'public' disk root (storage/app/public)
        $storageRelative = 'djs/previews/' . $finalName;

        // Log assembled upload for debugging/verification
        Log::info('Upload assembled', ['upload_id' => $uploadId, 'path' => $storageRelative]);

        return response()->json(['path' => $storageRelative]);
    }
}
