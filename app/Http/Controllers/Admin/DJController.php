<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DJ;

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
            'video_url' => 'required|url',
            'slot' => 'required|string|max:255|unique:djs,slot',
        ]);

        DJ::create($request->only(['name', 'video_url', 'slot']));

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
            'video_url' => 'required|url',
            'slot' => 'required|string|max:255|unique:djs,slot,' . $id,
        ]);

        $dj = DJ::findOrFail($id);
        $dj->update($request->only(['name', 'video_url', 'slot']));

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
