<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DJ;
use App\Models\Date;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get total counts
        $totalDJs = DJ::count();
        $totalDates = Date::count();

        // Get recently added DJs (last 5)
        $recentDJs = DJ::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch dates with DJ counts
        $dates = Date::orderBy('date', 'asc')
            ->get()
            ->map(function ($date) {
                $djCount = DJ::where('date_id', $date->id)->count();
                return [
                    'id' => $date->id,
                    'date' => $date->date,
                    'formatted_date' => $date->date->format('M d, Y'),
                    'day' => $date->date->format('d'),
                    'month' => $date->date->format('M'),
                    'year' => $date->date->format('Y'),
                    'day_name' => $date->date->format('D'),
                    'event_name' => $date->event_name,
                    'location' => $date->location,
                    'dj_count' => $djCount,
                ];
            });

        return view('admin.dashboard', compact('dates', 'totalDJs', 'totalDates', 'recentDJs'));
    }
}
