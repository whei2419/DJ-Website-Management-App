<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteEvent;

class AnalyticsController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'event' => 'required|string|max:191',
            'meta' => 'nullable|array',
        ]);

        $payload = [
            'event' => $data['event'],
            'meta' => $data['meta'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        SiteEvent::create($payload);

        return response()->json(['status' => 'ok'], 201);
    }
}
