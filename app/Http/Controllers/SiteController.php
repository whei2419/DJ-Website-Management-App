<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Date;
use App\Models\DJ;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    public function index()
    {
        return view('website.index');
    }

    public function gallery()
    {
        return view('website.gallery');
    }

    public function getDates()
    {
        $dates = Date::orderBy('date', 'desc')->get();
        return response()->json($dates);
    }

    public function getDJsByDate($dateId)
    {
        // Support both numeric date ID and date string (YYYY-MM-DD)
        try {
            if (is_numeric($dateId)) {
                $djs = DJ::where('date_id', $dateId)->where('visible', true)->get();
            } else {
                // attempt to find Date by date column
                $dateModel = Date::whereDate('date', $dateId)->first();
                if ($dateModel) {
                    $djs = DJ::where('date_id', $dateModel->id)->where('visible', true)->get();
                } else {
                    // no matching date found; return empty array
                    return response()->json([]);
                }
            }

            return response()->json($djs);
        } catch (\Throwable $e) {
            \Log::error('Error fetching DJs by date', ['date' => $dateId, 'error' => $e->getMessage()]);
            return response()->json([], 500);
        }
    }

    /**
     * Show a public share page for a DJ video.
     */
    public function showVideo($id)
    {
        try {
            $dj = DJ::findOrFail($id);

            // Determine source URLs (prefer HLS, then stored file)
            $videoUrl = null;
            $posterUrl = null;
            $hlsUrl = null;

            // Prefer HLS stream if available
            if ($dj->hls_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($dj->hls_path)) {
                $hlsUrl = Storage::disk('public')->url($dj->hls_path);
            }

            // Fall back to stored progressive files
            if ($dj->video_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($dj->video_path)) {
                $videoUrl = Storage::disk('public')->url($dj->video_path);
            } elseif ($dj->preview_video_path && Storage::disk('public')->exists($dj->preview_video_path)) {
                $videoUrl = Storage::disk('public')->url($dj->preview_video_path);
            } elseif (!empty($dj->video_url)) {
                $videoUrl = $dj->video_url;
            }

            if ($dj->poster_path && Storage::disk('public')->exists($dj->poster_path)) {
                $posterUrl = Storage::disk('public')->url($dj->poster_path);
            }

            return view('website.video', compact('dj', 'videoUrl', 'posterUrl', 'hlsUrl'));
        } catch (\Throwable $e) {
            \Log::error('Failed to show shared video', ['id' => $id, 'error' => $e->getMessage()]);
            abort(404);
        }
    }

    /**
     * Download the preview video for a DJ (only serves preview, not original full video).
     */
    public function downloadPreview($id)
    {
        try {
            $dj = DJ::findOrFail($id);

            // Only allow downloading the preview video
            if ($dj->preview_video_path && Storage::disk('public')->exists($dj->preview_video_path)) {
                $fileName = basename($dj->preview_video_path);
                return Storage::disk('public')->download($dj->preview_video_path, $fileName);
            }

            // If no preview available, return 404
            abort(404, 'Preview not available');
        } catch (\Throwable $e) {
            \Log::error('Failed to download preview', ['id' => $id, 'error' => $e->getMessage()]);
            abort(404);
        }
    }
}
