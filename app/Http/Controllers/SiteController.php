<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Date;
use App\Models\DJ;

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
                $djs = DJ::where('date_id', $dateId)->get();
            } else {
                // attempt to find Date by date column
                $dateModel = Date::whereDate('date', $dateId)->first();
                if ($dateModel) {
                    $djs = DJ::where('date_id', $dateModel->id)->get();
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
}
