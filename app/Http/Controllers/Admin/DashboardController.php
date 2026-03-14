<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $lastMonth = $now->copy()->subMonth();

        // ── Core Counts ──────────────────────────────────────
        $totalHotels = Hotel::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'paid'])->sum('total_amount');
        $activeDomains = Domain::where('is_active', true)->count();
        $totalLocations = Location::where('is_active', true)->count();
        $totalReviews = Review::where('is_approved', true)->count();
        $totalRoomTypes = RoomType::count();
        $totalUsers = User::count();

        // ── Today's Stats ────────────────────────────────────
        $todayRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $today)->sum('total_amount');
        $todayBookings = Booking::where('created_at', '>=', $today)->count();
        $yesterdayRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->whereBetween('created_at', [$today->copy()->subDay(), $today])->sum('total_amount');
        $yesterdayBookings = Booking::whereBetween('created_at', [$today->copy()->subDay(), $today])->count();

        // ── This Week ────────────────────────────────────────
        $weekRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $weekBookings = Booking::where('created_at', '>=', $startOfWeek)->count();

        // ── This Month Stats ─────────────────────────────────
        $monthBookings = Booking::where('created_at', '>=', $startOfMonth)->count();
        $monthRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfMonth)->sum('total_amount');

        // ── Year to Date ─────────────────────────────────────
        $ytdRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfYear)->sum('total_amount');

        // ── Last Month Comparison ────────────────────────────
        $lastMonthBookings = Booking::whereBetween('created_at', [
            $lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth(),
        ])->count();
        $lastMonthRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->whereBetween('created_at', [
                $lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth(),
            ])->sum('total_amount');

        // ── Growth Percentages ───────────────────────────────
        $bookingGrowth = $lastMonthBookings > 0
            ? round((($monthBookings - $lastMonthBookings) / $lastMonthBookings) * 100, 1)
            : ($monthBookings > 0 ? 100 : 0);
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthRevenue > 0 ? 100 : 0);

        // Today's growth vs yesterday
        $todayRevenueGrowth = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        // ── Average Booking Value ────────────────────────────
        $avgBookingValue = Booking::whereIn('status', ['confirmed', 'paid'])->avg('total_amount') ?? 0;
        $avgNightsStay = Booking::whereIn('status', ['confirmed', 'paid'])->avg('num_nights') ?? 0;

        // ── Cancellation Rate ────────────────────────────────
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0;

        // ── Pending Actions ──────────────────────────────────
        $pendingBookings = Booking::where('status', 'pending')->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        // ── Booking Status Breakdown ─────────────────────────
        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Revenue Last 30 Days (Daily) ─────────────────────
        $revenueChart30 = [];
        $bookingsChart30 = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');
            $dayBookings = Booking::whereDate('created_at', $date->toDateString())->count();
            $revenueChart30[] = [
                'label' => $date->format('M d'),
                'value' => round($dayRevenue, 2),
            ];
            $bookingsChart30[] = [
                'label' => $date->format('M d'),
                'value' => $dayBookings,
            ];
        }

        // ── Monthly Revenue Trend (Last 12 Months) ──────────
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
            $mRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            $mBookings = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $monthlyRevenue[] = [
                'label' => $monthStart->format('M Y'),
                'short' => $monthStart->format('M'),
                'revenue' => round($mRevenue, 2),
                'bookings' => $mBookings,
            ];
        }

        // ── Revenue by Domain (Top 10) ──────────────────────
        $revenueByDomain = Domain::select('domains.id', 'domains.name', 'domains.domain')
            ->leftJoin('bookings', function ($join) {
                $join->on('domains.id', '=', 'bookings.domain_id')
                    ->whereIn('bookings.status', ['confirmed', 'paid'])
                    ->whereNull('bookings.deleted_at');
            })
            ->where('domains.is_active', true)
            ->whereNull('domains.deleted_at')
            ->groupBy('domains.id', 'domains.name', 'domains.domain')
            ->selectRaw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // ── Hotel Earnings Table (Top 15) ────────────────────
        $hotelEarnings = Hotel::select('hotels.id', 'hotels.name', 'hotels.star_rating', 'hotels.location_id', 'hotels.avg_rating', 'hotels.is_active')
            ->leftJoin('bookings', function ($join) {
                $join->on('hotels.id', '=', 'bookings.hotel_id')
                    ->whereIn('bookings.status', ['confirmed', 'paid'])
                    ->whereNull('bookings.deleted_at');
            })
            ->with('location:id,name')
            ->whereNull('hotels.deleted_at')
            ->groupBy('hotels.id', 'hotels.name', 'hotels.star_rating', 'hotels.location_id', 'hotels.avg_rating', 'hotels.is_active')
            ->selectRaw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->selectRaw('COALESCE(AVG(bookings.total_amount), 0) as avg_booking_value')
            ->orderByDesc('total_revenue')
            ->take(15)
            ->get();

        // ── Revenue by Location (Top 10) ─────────────────────
        $revenueByLocation = Location::select('locations.id', 'locations.name')
            ->leftJoin('hotels', function ($join) {
                $join->on('locations.id', '=', 'hotels.location_id')
                    ->whereNull('hotels.deleted_at');
            })
            ->leftJoin('bookings', function ($join) {
                $join->on('hotels.id', '=', 'bookings.hotel_id')
                    ->whereIn('bookings.status', ['confirmed', 'paid'])
                    ->whereNull('bookings.deleted_at');
            })
            ->where('locations.is_active', true)
            ->whereNull('locations.deleted_at')
            ->groupBy('locations.id', 'locations.name')
            ->selectRaw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue')
            ->selectRaw('COUNT(DISTINCT hotels.id) as hotel_count')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // ── Bookings by Day of Week ──────────────────────────
        $bookingsByDayOfWeek = Booking::selectRaw('DAYOFWEEK(created_at) as day_num, count(*) as count')
            ->groupByRaw('DAYOFWEEK(created_at)')
            ->pluck('count', 'day_num')
            ->toArray();
        $daysMap = [1 => 'Sun', 2 => 'Mon', 3 => 'Tue', 4 => 'Wed', 5 => 'Thu', 6 => 'Fri', 7 => 'Sat'];
        $bookingsByDay = [];
        for ($d = 1; $d <= 7; $d++) {
            $bookingsByDay[] = [
                'label' => $daysMap[$d],
                'value' => $bookingsByDayOfWeek[$d] ?? 0,
            ];
        }

        // ── Guest Nationality Stats (Top 10) ────────────────
        $guestNationality = Booking::select('guest_nationality', DB::raw('count(*) as count'))
            ->whereNotNull('guest_nationality')
            ->where('guest_nationality', '!=', '')
            ->groupBy('guest_nationality')
            ->orderByDesc('count')
            ->take(10)
            ->pluck('count', 'guest_nationality')
            ->toArray();

        // ── Revenue by Room Type (Top 10) ────────────────────
        $revenueByRoomType = RoomType::select('room_types.id', 'room_types.name')
            ->leftJoin('bookings', function ($join) {
                $join->on('room_types.id', '=', 'bookings.room_type_id')
                    ->whereIn('bookings.status', ['confirmed', 'paid'])
                    ->whereNull('bookings.deleted_at');
            })
            ->whereNull('room_types.deleted_at')
            ->groupBy('room_types.id', 'room_types.name')
            ->selectRaw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // ── Top Hotels (by bookings count) ───────────────────
        $topHotels = Hotel::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        // ── Top Locations ────────────────────────────────────
        $topLocations = Location::withCount('hotels')
            ->where('is_active', true)
            ->orderByDesc('hotels_count')
            ->take(5)
            ->get();

        // ── Domain Performance Table ─────────────────────────
        $domainPerformance = Domain::where('is_active', true)
            ->withCount(['bookings', 'hotels'])
            ->get()
            ->each(function ($domain) {
                $domain->domain_revenue = Booking::where('domain_id', $domain->id)
                    ->whereIn('status', ['confirmed', 'paid'])
                    ->sum('total_amount');
            })
            ->sortByDesc('domain_revenue')
            ->take(10)
            ->values();

        // ── Payment Status Breakdown ─────────────────────────
        $paymentStats = Payment::select('status', DB::raw('count(*) as count'), DB::raw('COALESCE(SUM(amount), 0) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // ── Recent Bookings ──────────────────────────────────
        $recentBookings = Booking::with(['hotel', 'domain'])
            ->latest()
            ->take(10)
            ->get();

        // ── Recent Reviews ───────────────────────────────────
        $recentReviews = Review::with('hotel')
            ->latest()
            ->take(5)
            ->get();

        // ── Hourly Booking Pattern (last 30 days) ────────────
        $hourlyBookings = Booking::selectRaw('HOUR(created_at) as hour, count(*) as count')
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->groupByRaw('HOUR(created_at)')
            ->pluck('count', 'hour')
            ->toArray();
        $hourlyData = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyData[] = [
                'label' => sprintf('%02d:00', $h),
                'value' => $hourlyBookings[$h] ?? 0,
            ];
        }

        return view('admin.dashboard', compact(
            'totalHotels', 'totalBookings', 'totalRevenue', 'activeDomains',
            'totalLocations', 'totalReviews', 'totalRoomTypes', 'totalUsers',
            'todayRevenue', 'todayBookings', 'todayRevenueGrowth',
            'weekRevenue', 'weekBookings',
            'monthBookings', 'monthRevenue', 'bookingGrowth', 'revenueGrowth',
            'ytdRevenue', 'avgBookingValue', 'avgNightsStay', 'cancellationRate',
            'pendingBookings', 'pendingReviews',
            'bookingsByStatus', 'bookingsByDay',
            'revenueChart30', 'bookingsChart30', 'monthlyRevenue',
            'revenueByDomain', 'hotelEarnings', 'revenueByLocation', 'revenueByRoomType',
            'topHotels', 'topLocations', 'domainPerformance',
            'guestNationality', 'paymentStats', 'hourlyData',
            'recentBookings', 'recentReviews'
        ));
    }
}
