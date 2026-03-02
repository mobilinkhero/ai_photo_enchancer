<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use Illuminate\Http\Request;

class ApiLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = ApiLog::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('ai_provider', $request->provider);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($b) use ($q) {
                $b->where('request_id', 'like', "%$q%")
                    ->orWhere('user_uid', 'like', "%$q%")
                    ->orWhere('client_endpoint', 'like', "%$q%")
                    ->orWhere('error_message', 'like', "%$q%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        // Summary stats
        $stats = [
            'total' => ApiLog::count(),
            'success' => ApiLog::where('status', 'success')->count(),
            'error' => ApiLog::where('status', 'error')->count(),
            'timeout' => ApiLog::where('status', 'timeout')->count(),
            'avg_time' => round(ApiLog::whereNotNull('total_time_ms')->avg('total_time_ms')),
            'avg_ai_time' => round(ApiLog::whereNotNull('ai_response_time_ms')->avg('ai_response_time_ms')),
        ];

        return view('admin.api_logs.index', compact('logs', 'stats'));
    }

    public function show(ApiLog $apiLog)
    {
        return view('admin.api_logs.show', compact('apiLog'));
    }

    public function destroy(ApiLog $apiLog)
    {
        $apiLog->delete();
        return redirect()->route('admin.api-logs.index')->with('success', 'Log deleted.');
    }

    public function clear(Request $request)
    {
        $count = ApiLog::count();
        ApiLog::truncate();
        return redirect()->route('admin.api-logs.index')
            ->with('success', "Cleared {$count} log entries.");
    }
}
