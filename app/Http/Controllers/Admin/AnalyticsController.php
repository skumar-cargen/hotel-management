<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Domain;
use App\Models\DomainAnalytics;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $domains = Domain::active()->get();
        $domainId = $request->domain_id;
        $dateFrom = $request->date_from ?? now()->subDays(30)->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $analytics = DomainAnalytics::when($domainId, fn ($q, $v) => $q->where('domain_id', $v))
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->get();

        $summary = [
            'total_revenue' => $analytics->sum('revenue'),
            'total_bookings' => $analytics->sum('booking_completions'),
            'total_page_views' => $analytics->sum('page_views'),
            'avg_booking_value' => $analytics->sum('booking_completions') > 0
                ? $analytics->sum('revenue') / $analytics->sum('booking_completions')
                : 0,
        ];

        $bookingQuery = Booking::when($domainId, fn ($q, $v) => $q->where('domain_id', $v))
            ->whereBetween('created_at', [$dateFrom, $dateTo.' 23:59:59']);

        // Revenue by Domain
        $revenueByDomain = (clone $bookingQuery)
            ->whereIn('status', ['confirmed', 'paid'])
            ->select('domain_id', DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('domain_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($row) {
                $domain = Domain::find($row->domain_id);

                return [
                    'name' => $domain?->name ?? 'Unknown',
                    'revenue' => (float) $row->revenue,
                ];
            })
            ->values();

        // Revenue by Hotel (top 10)
        $revenueByHotel = (clone $bookingQuery)
            ->whereIn('status', ['confirmed', 'paid'])
            ->select('hotel_id', DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('hotel_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $hotel = Hotel::find($row->hotel_id);

                return [
                    'name' => $hotel?->name ?? 'Unknown',
                    'revenue' => (float) $row->revenue,
                ];
            })
            ->values();

        // Hotel Performance (top 10 by booking count)
        $hotelPerformance = (clone $bookingQuery)
            ->select(
                'hotel_id',
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('AVG(total_amount) as avg_value')
            )
            ->groupBy('hotel_id')
            ->orderByDesc('bookings')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $hotel = Hotel::find($row->hotel_id);

                return [
                    'name' => $hotel?->name ?? 'Unknown',
                    'bookings' => (int) $row->bookings,
                    'revenue' => (float) $row->revenue,
                    'avg_value' => round((float) $row->avg_value, 2),
                    'rating' => (float) ($hotel?->avg_rating ?? 0),
                ];
            })
            ->values();

        // Bookings by Status
        $bookingsByStatus = (clone $bookingQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => ucfirst($row->status instanceof \App\Enums\BookingStatus ? $row->status->value : $row->status),
                'count' => (int) $row->count,
            ])
            ->values();

        // SEO Summary
        $seoSummary = [
            'total_organic_traffic' => $analytics->sum('organic_traffic'),
            'total_impressions' => $analytics->sum('search_impressions'),
            'total_clicks' => $analytics->sum('search_clicks'),
            'avg_ctr' => $analytics->sum('search_impressions') > 0
                ? round(($analytics->sum('search_clicks') / $analytics->sum('search_impressions')) * 100, 2)
                : 0,
            'avg_position' => round($analytics->where('avg_position', '>', 0)->avg('avg_position') ?? 0, 2),
            'avg_bounce_rate' => round($analytics->where('bounce_rate', '>', 0)->avg('bounce_rate') ?? 0, 2),
        ];

        // Top Keywords — merge all per-day top_keywords, sum clicks/impressions, sort by clicks desc, take top 10
        $topKeywords = $analytics
            ->pluck('top_keywords')
            ->filter()
            ->flatten(1)
            ->groupBy('keyword')
            ->map(function ($items, $keyword) {
                $clicks = $items->sum('clicks');
                $impressions = $items->sum('impressions');

                return [
                    'keyword' => $keyword,
                    'clicks' => $clicks,
                    'impressions' => $impressions,
                    'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0,
                    'position' => round($items->avg('position'), 2),
                ];
            })
            ->sortByDesc('clicks')
            ->take(10)
            ->values();

        // Top Landing Pages — merge all per-day top_landing_pages, sum views, avg bounce_rate, take top 10
        $topLandingPages = $analytics
            ->pluck('top_landing_pages')
            ->filter()
            ->flatten(1)
            ->groupBy('page')
            ->map(function ($items, $page) {
                return [
                    'page' => $page,
                    'views' => $items->sum('views'),
                    'bounce_rate' => round($items->avg('bounce_rate'), 2),
                ];
            })
            ->sortByDesc('views')
            ->take(10)
            ->values();

        return view('admin.analytics.index', compact(
            'domains', 'analytics', 'summary', 'dateFrom', 'dateTo', 'domainId',
            'revenueByDomain', 'revenueByHotel', 'hotelPerformance', 'bookingsByStatus',
            'seoSummary', 'topKeywords', 'topLandingPages'
        ));
    }
}
