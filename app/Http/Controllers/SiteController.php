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
        $djs = DJ::where('date_id', $dateId)->get();
        return response()->json($djs);
    }
}
