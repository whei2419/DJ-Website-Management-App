<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DJ;
use Illuminate\Support\Facades\Storage;

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
                $videoPreview = $dj->video_path
                    ? (Storage::disk('public')->exists($dj->video_path) ? Storage::disk('public')->url($dj->video_path) : null)
                    : ($dj->video_url ?? null);

                return [
                    'id' => $dj->id,
                    'video_preview' => $videoPreview,
                    'name' => $dj->name,
                    'slot' => $dj->slot ?? '-',
                    'actions' => view('admin.djs.partials.actions', ['dj' => $dj])->render(),
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
        $request->validate([
            'name' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm|max:200000',
        ]);

        $data = $request->only(['name']);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $path = $file->store('djs', 'public');
            $data['video_path'] = $path;
        }

        if ($request->filled('video_url')) {
            $data['video_url'] = $request->input('video_url');
        }

        DJ::create($data);

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
            // delete previous file if exists
            if ($dj->video_path && Storage::disk('public')->exists($dj->video_path)) {
                Storage::disk('public')->delete($dj->video_path);
            }
            $file = $request->file('video');
            $path = $file->store('djs', 'public');
            $data['video_path'] = $path;
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
        //
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
