<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DJ;
use Illuminate\Support\Facades\Storage;
use App\Services\VideoPreviewService;

class DJController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $djs = DJ::all();
        return view('admin.djs.index', compact('djs'));
    }

    /**
     * Get available dates with DJ counts for the date selector.
     */
    public function availableDates(Request $request)
    {
        try {
            $dates = \App\Models\Date::orderBy('date', 'asc')
                ->get()
                ->map(function ($date) {
                    $djCount = DJ::where('slot', $date->date->format('Y-m-d'))->count();
                    return [
                        'id' => $date->id,
                        'date' => $date->date->format('Y-m-d'),
                        'formatted_date' => $date->date->format('M d, Y'),
                        'event_name' => $date->event_name,
                        'location' => $date->location,
                        'dj_count' => $djCount,
                    ];
                });

            return response()->json(['data' => $dates]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load dates',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return DJs for AJAX with search and pagination.
     */
    public function list(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 20);
            $search = $request->input('search');

            $query = DJ::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slot', 'like', "%{$search}%");
                });
            }

            $query->orderBy('name', 'asc');

            $paginated = $query->paginate($perPage);

            $data = $paginated->getCollection()->map(function ($dj) {
                // Prefer preview video over original for performance
                $videoPreview = null;
                $posterUrl = null;

                if ($dj->preview_video_path && Storage::disk('public')->exists($dj->preview_video_path)) {
                    $videoPreview = Storage::disk('public')->url($dj->preview_video_path);
                } elseif ($dj->video_path && Storage::disk('public')->exists($dj->video_path)) {
                    $videoPreview = Storage::disk('public')->url($dj->video_path);
                } elseif ($dj->video_url) {
                    $videoPreview = $dj->video_url;
                }

                if ($dj->poster_path && Storage::disk('public')->exists($dj->poster_path)) {
                    $posterUrl = Storage::disk('public')->url($dj->poster_path);
                }

                return [
                    'id' => $dj->id,
                    'video_preview' => $videoPreview,
                    'poster' => $posterUrl,
                    'name' => $dj->name,
                    'slot' => $dj->slot ?? '-',
                    'actions' => view('admin.djs.partials.actions', ['dj' => $dj])->render(),
                ];
            })->values();

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $paginated->total(),
                'recordsFiltered' => $paginated->total(),
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load DJs',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.djs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the MIME type before validation to debug
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            \Log::info('Uploaded video MIME type: ' . $file->getMimeType());
            \Log::info('Uploaded video client original name: ' . $file->getClientOriginalName());
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slot' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm,video/x-matroska|max:512000', // 500MB
        ]);

        $data = $request->only(['name', 'slot']);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $path = $file->store('djs', 'public');
            $data['video_path'] = $path;

            // Generate preview video and poster
            $videoService = new VideoPreviewService();
            $previewPath = $videoService->generatePreview($path);
            $posterPath = $videoService->generatePoster($path);

            if ($previewPath) {
                $data['preview_video_path'] = $previewPath;
            }
            if ($posterPath) {
                $data['poster_path'] = $posterPath;
            }
        }

        if ($request->filled('video_url')) {
            $data['video_url'] = $request->input('video_url');
        }

        $dj = DJ::create($data);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'dj' => $dj], 201);
        }

        return redirect()->route('admin.djs.index')->with('success', 'DJ created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dj = DJ::findOrFail($id);
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json(['dj' => $dj]);
        }
        
        return view('admin.djs.edit', compact('dj'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm|max:200000',
            'slot' => 'nullable|string',
        ]);

        $dj = DJ::findOrFail($id);

        $data = $request->only(['name']);

        if ($request->filled('slot')) {
            $data['slot'] = $request->input('slot');
        }

        if ($request->hasFile('video')) {
            // delete previous files if exists
            if ($dj->video_path && Storage::disk('public')->exists($dj->video_path)) {
                Storage::disk('public')->delete($dj->video_path);
                
                // Delete preview files
                $videoService = new VideoPreviewService();
                $videoService->deletePreviewFiles($dj->video_path);
            }
            
            $file = $request->file('video');
            $path = $file->store('djs', 'public');
            $data['video_path'] = $path;

            // Generate new preview video and poster
            $videoService = new VideoPreviewService();
            $previewPath = $videoService->generatePreview($path);
            $posterPath = $videoService->generatePoster($path);

            if ($previewPath) {
                $data['preview_video_path'] = $previewPath;
            }
            if ($posterPath) {
                $data['poster_path'] = $posterPath;
            }
        }

        if ($request->filled('video_url')) {
            $data['video_url'] = $request->input('video_url');
        }

        $dj->update($data);

        return redirect()->route('admin.djs.index')->with('success', 'DJ information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $dj = DJ::findOrFail($id);
            
            // Delete video file if exists
            if ($dj->video_path && Storage::disk('public')->exists($dj->video_path)) {
                Storage::disk('public')->delete($dj->video_path);
                
                // Delete preview files
                $videoService = new VideoPreviewService();
                $videoService->deletePreviewFiles($dj->video_path);
            }
            
            $dj->delete();
            
            // Return JSON for AJAX requests
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'DJ deleted successfully']);
            }
            
            return redirect()->route('admin.djs.index')->with('success', 'DJ deleted successfully.');
        } catch (\Throwable $e) {
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('admin.djs.index')->with('error', 'Failed to delete DJ.');
        }
    }

    /**
     * Switch time slots between two DJs.
     */
    public function switchSlots(Request $request)
    {
        $request->validate([
            'dj1_id' => 'required|exists:djs,id',
            'dj2_id' => 'required|exists:djs,id',
        ]);

        $dj1 = DJ::findOrFail($request->dj1_id);
        $dj2 = DJ::findOrFail($request->dj2_id);

        $tempSlot = $dj1->slot;
        $dj1->slot = $dj2->slot;
        $dj2->slot = $tempSlot;

        $dj1->save();
        $dj2->save();

        return redirect()->route('admin.djs.index')->with('success', 'Time slots switched successfully.');
    }
}
