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
            'date_id' => 'nullable|integer|exists:dates,id',
            'slot' => 'nullable|string|max:100',
        ]);

        // Do not persist `slot` column; map to date_id if provided
        $create = [
            'name' => $data['name'],
            'video_url' => $data['video_url'] ?? null,
        ];

        if (!empty($data['date_id'])) {
            $create['date_id'] = (int) $data['date_id'];
        } elseif (!empty($data['slot'])) {
            if (is_numeric($data['slot'])) {
                $create['date_id'] = (int) $data['slot'];
            } else {
                $dateModel = \App\Models\Date::whereDate('date', $data['slot'])->first();
                if ($dateModel) $create['date_id'] = $dateModel->id;
            }
        }

        DJ::create($create);

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
            'date_id' => 'nullable|integer|exists:dates,id',
            'slot' => 'nullable|string|max:100',
        ]);

        $update = ['name' => $data['name']];
        if (array_key_exists('video_url', $data)) $update['video_url'] = $data['video_url'];

        if (!empty($data['date_id'])) {
            $update['date_id'] = (int) $data['date_id'];
        } elseif (!empty($data['slot'])) {
            if (is_numeric($data['slot'])) {
                $update['date_id'] = (int) $data['slot'];
            } else {
                $dateModel = \App\Models\Date::whereDate('date', $data['slot'])->first();
                if ($dateModel) $update['date_id'] = $dateModel->id;
            }
        }

        $dj->update($update);

        return redirect()->route('admin.djs.index')->with('success', 'DJ updated');
    }

    public function destroy(DJ $dj)
    {
        $dj->delete();
        return redirect()->route('admin.djs.index')->with('success', 'DJ deleted');
    }
}
