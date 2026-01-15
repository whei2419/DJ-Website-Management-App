<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Http\Request;

class DateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dates = Date::orderBy('date', 'asc')->get();
        return view('admin.dates.index', compact('dates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date = Date::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Date created successfully.',
                'date' => [
                    'id' => $date->id,
                    'date' => $date->date->format('Y-m-d'),
                    'date_formatted' => $date->date->format('Y-m-d (l)'),
                ],
            ]);
        }

        return redirect()->route('admin.dates.index')->with('success', 'Date created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Date $date)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Date updated successfully.',
                'date' => [
                    'id' => $date->id,
                    'date' => $date->date->format('Y-m-d'),
                    'date_formatted' => $date->date->format('Y-m-d (l)'),
                ],
            ]);
        }

        return redirect()->route('admin.dates.index')->with('success', 'Date updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Date $date)
    {
        $date->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Date deleted successfully.']);
        }

        return redirect()->route('admin.dates.index')->with('success', 'Date deleted successfully.');
    }
}
