<?php

namespace App\Http\Controllers;

use App\Models\DJ;
use Illuminate\Http\Request;

class DJController extends Controller
{
    public function index()
    {
        if (!\Schema::hasTable((new DJ)->getTable())) {
            $djs = collect();
        } else {
            $djs = DJ::orderBy('created_at', 'desc')->get();
        }

        return view('admin.djs.index', compact('djs'));
    }

    public function create()
    {
        return view('admin.djs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'video_url' => 'nullable|url|max:1000',
            'slot' => 'nullable|string|max:100',
        ]);

        DJ::create($data);

        return redirect()->route('admin.djs.index')->with('success', 'DJ created');
    }

    public function edit(DJ $dj)
    {
        return view('admin.djs.edit', compact('dj'));
    }

    public function update(Request $request, DJ $dj)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'video_url' => 'nullable|url|max:1000',
            'slot' => 'nullable|string|max:100',
        ]);

        $dj->update($data);

        return redirect()->route('admin.djs.index')->with('success', 'DJ updated');
    }

    public function destroy(DJ $dj)
    {
        $dj->delete();
        return redirect()->route('admin.djs.index')->with('success', 'DJ deleted');
    }
}
