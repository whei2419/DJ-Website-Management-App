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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slot' => 'required|string|max:255',
                'video' => 'required|file|mimes:mp4,mov,avi,webm,quicktime,x-msvideo,x-ms-wmv|max:102400',
            ]);

            $path = null;
            if ($request->hasFile('video')) {
                $path = $request->file('video')->store('djs', 'public');
            }

            $dj = DJ::create([
                'name' => $validated['name'],
                'slot' => $validated['slot'],
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
