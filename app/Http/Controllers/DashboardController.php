<?php

namespace App\Http\Controllers;

use App\Models\MoveEvent;
use App\Models\MovingQuote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const CHART_WEEKS = 6;

    public function index(Request $request)
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        $leadsToday = MovingQuote::where('created_at', '>=', $today)->count();
        $leadsYesterday = MovingQuote::whereBetween('created_at', [$today->copy()->subDay(), $today])->count();

        $weekStart = $now->copy()->startOfWeek();
        $leadsThisWeek = MovingQuote::where('created_at', '>=', $weekStart)->count();
        $leadsLastWeek = MovingQuote::whereBetween('created_at', [$weekStart->copy()->subWeek(), $weekStart])->count();

        $upcomingJobs = MoveEvent::active()->where('start_at', '>=', $now)->count();
        $jobsToday = MoveEvent::active()
            ->whereBetween('start_at', [$today, $today->copy()->endOfDay()])
            ->count();

        $monthEstimate = MovingQuote::where('created_at', '>=', $now->copy()->startOfMonth())
            ->where('status', '!=', 'cancelled')
            ->sum('estimate_total');

        $recentQuotes = MovingQuote::latest()->limit(6)->get();

        $agenda = MoveEvent::active()
            ->whereBetween('start_at', [$today, $today->copy()->endOfDay()])
            ->orderBy('start_at')
            ->get();

        $weekly = collect(range(self::CHART_WEEKS - 1, 0))->map(function ($ago) use ($weekStart) {
            $start = $weekStart->copy()->subWeeks($ago);

            return [
                'label' => $start->format('M j'),
                'count' => MovingQuote::whereBetween('created_at', [$start, $start->copy()->addWeek()])->count(),
                'current' => $ago === 0,
            ];
        });

        return view('dashboard', [
            'stats' => [
                'leadsToday' => $leadsToday,
                'leadsTodayDelta' => $leadsToday - $leadsYesterday,
                'leadsWeek' => $leadsThisWeek,
                'leadsWeekDelta' => $leadsThisWeek - $leadsLastWeek,
                'upcomingJobs' => $upcomingJobs,
                'jobsToday' => $jobsToday,
                'monthEstimate' => (float) $monthEstimate,
            ],
            'recentQuotes' => $recentQuotes,
            'agenda' => $agenda,
            'weekly' => $weekly,
            'weeklyMax' => max(1, $weekly->max('count')),
        ]);
    }

    public function leads(Request $request)
    {
        $quotes = MovingQuote::all();

        return view('dashboard-leads', compact('quotes'));
    }
}
