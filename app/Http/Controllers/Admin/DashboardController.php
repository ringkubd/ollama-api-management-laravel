<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiRequest;
use App\Models\OllamaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        $timeRange = $request->input('time_range', 'day');
        $startDate = $this->getStartDate($timeRange);

        // Get total request count
        $totalRequests = ApiRequest::count();

        // Get requests in the selected time period
        $periodRequests = ApiRequest::where('created_at', '>=', $startDate)->count();

        // Get request counts by status for the selected period
        $requestsByStatus = ApiRequest::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get error rate
        $errorRate = 0;
        if ($periodRequests > 0) {
            $failedRequests = $requestsByStatus['failed'] ?? 0;
            $errorRate = round(($failedRequests / $periodRequests) * 100, 2);
        }

        // Get average response time
        $averageResponseTime = ApiRequest::where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->avg('response_time') ?? 0;

        // Get top models by request count
        $topModels = OllamaModel::orderBy('request_count', 'desc')
            ->take(5)
            ->get();

        // Get top API keys by request count
        $topApiKeys = ApiKey::orderBy('request_count', 'desc')
            ->take(5)
            ->get();

        // Get total active/inactive API keys
        $totalApiKeys = ApiKey::count();
        $activeApiKeys = ApiKey::where('is_active', true)->count();

        // Get requests over time data for chart
        $requestsOverTime = $this->getRequestsOverTime($timeRange, $startDate);

        // Get recent errors
        $recentErrors = ApiRequest::where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get request distribution by endpoint
        $requestsByEndpoint = ApiRequest::where('created_at', '>=', $startDate)
            ->select('endpoint', DB::raw('count(*) as count'))
            ->groupBy('endpoint')
            ->pluck('count', 'endpoint')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalRequests',
            'periodRequests',
            'requestsByStatus',
            'errorRate',
            'averageResponseTime',
            'topModels',
            'topApiKeys',
            'totalApiKeys',
            'activeApiKeys',
            'requestsOverTime',
            'recentErrors',
            'requestsByEndpoint',
            'timeRange'
        ));
    }

    /**
     * Calculate the start date based on the selected time range.
     */
    private function getStartDate($timeRange)
    {
        return match ($timeRange) {
            'day' => Carbon::now()->subDay(),
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subDay(),
        };
    }

    /**
     * Get request data over time for charting.
     */
    private function getRequestsOverTime($timeRange, $startDate)
    {
        $format = $this->getTimeFormat($timeRange);
        $groupBy = $this->getGroupBy($timeRange);

        return ApiRequest::where('created_at', '>=', $startDate)
            ->select(DB::raw("DATE_FORMAT(created_at, '{$format}') as time_period"), DB::raw('count(*) as count'))
            ->groupBy('time_period')
            ->orderBy('time_period')
            ->pluck('count', 'time_period')
            ->toArray();
    }

    /**
     * Get the appropriate date/time format for the selected time range.
     */
    private function getTimeFormat($timeRange)
    {
        return match ($timeRange) {
            'day' => '%H:00',          // Hours
            'week' => '%Y-%m-%d',      // Days
            'month' => '%Y-%m-%d',     // Days
            'year' => '%Y-%m',         // Months
            default => '%H:00',
        };
    }

    /**
     * Get the appropriate group by clause for the selected time range.
     */
    private function getGroupBy($timeRange)
    {
        return match ($timeRange) {
            'day' => "HOUR(created_at)",
            'week' => "DATE(created_at)",
            'month' => "DATE(created_at)",
            'year' => "YEAR(created_at), MONTH(created_at)",
            default => "HOUR(created_at)",
        };
    }
}
