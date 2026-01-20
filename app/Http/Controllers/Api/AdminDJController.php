<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DJ;
use Illuminate\Support\Facades\Storage;

class AdminDJController extends Controller
{
    /**
     * Return DJs for API with search and pagination.
     * Query params:
     * - search: string
     * - per_page: int
     * - page: int
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
                $videoPreview = null;
                if (!empty($dj->video_path) && Storage::disk('public')->exists($dj->video_path)) {
                    $videoPreview = Storage::disk('public')->url($dj->video_path);
                } elseif (!empty($dj->video_url)) {
                    $videoPreview = $dj->video_url;
                }

                return [
                    'id' => $dj->id,
                    'video_preview' => $videoPreview,
                    'name' => $dj->name,
                    'slot' => $dj->slot ?? '-',
                    'actions' => [
                        'edit_url' => route('admin.djs.edit', $dj->id),
                        'destroy_url' => route('admin.djs.destroy', $dj->id),
                    ],
                ];
            })->values();

            return response()->json([
                'data' => $data,
                'meta' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load DJs',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new DJ (accepts optional video upload).
     */
    public function store(Request $request)
    {
        try {
            // Log the MIME type before validation to debug
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                \Log::info('Uploaded video MIME type: ' . $file->getMimeType());
                \Log::info('Uploaded video client original name: ' . $file->getClientOriginalName());
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slot' => 'required|string|max:255',
                'video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm|max:102400',
            ]);

            $path = null;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $path = $file->store('djs', 'public');
            }

            // Determine date_id: prefer explicit `date_id` from request, otherwise resolve from slot
            $dateId = null;
            if ($request->filled('date_id')) {
                $dateId = (int) $request->input('date_id');
            } elseif (is_numeric($validated['slot'])) {
                $dateId = (int) $validated['slot'];
            } else {
                $dateModel = \App\Models\Date::whereDate('date', $validated['slot'])->first();
                if ($dateModel) $dateId = $dateModel->id;
            }

            $dj = DJ::create([
                'name' => $validated['name'],
                'slot' => $validated['slot'],
                'date_id' => $dateId,
                'video_path' => $path,
            ]);

            return response()->json(['data' => $dj], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to save DJ',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
