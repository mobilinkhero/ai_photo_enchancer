<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\AppConfig;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_photos' => Photo::count(),
            'photos_today' => Photo::whereDate('created_at', today())->count(),
            'failed_photos' => Photo::where('status', 'failed')->count(),
            'active_provider' => AppConfig::get('ai_provider', 'replicate'),
            'recent_photos' => Photo::latest()->take(5)->get(),
        ];

        // Chart data: photos per day for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'label' => $date->format('M d'),
                'count' => Photo::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard.index', compact('stats', 'chartData'));
    }
}
