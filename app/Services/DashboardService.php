<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get all dashboard data in a single call.
     *
     * Returns an array whose keys match the view variables passed via compact() in
     * DashboardController::index():
     *
     *   totalHotels, totalBookings, totalRevenue, activeDomains,
     *   totalLocations, totalReviews, totalRoomTypes, totalUsers,
     *   todayRevenue, todayBookings, todayRevenueGrowth,
     *   weekRevenue, weekBookings,
     *   monthBookings, monthRevenue, bookingGrowth, revenueGrowth,
     *   ytdRevenue, avgBookingValue, avgNightsStay, cancellationRate,
     *   pendingBookings, pendingReviews,
     *   bookingsByStatus, bookingsByDay,
     *   revenueChart30, bookingsChart30, monthlyRevenue,
     *   revenueByDomain, hotelEarnings, revenueByLocation, revenueByRoomType,
     *   topHotels, topLocations, domainPerformance,
     *   guestNationality, paymentStats, hourlyData,
     *   recentBookings, recentReviews
     */
    public function getAllDashboardData(): array
    {
        $overview = $this->getOverviewMetrics();
        $todayStats = $this->getTodayStats();
        $periodStats = $this->getPeriodStats();
        $growth = $this->getGrowthMetrics($periodStats, $todayStats);
        $averages = $this->getAverageMetrics();
        $pending = $this->getPendingActions();
        $statusBreakdown = $this->getBookingStatusBreakdown();
        $revenueCharts = $this->getRevenueCharts(30);
        $monthlyRevenue = $this->getMonthlyRevenueTrend(12);
        $revenueByDomain = $this->getTopDomainsByRevenue(10);
        $hotelEarnings = $this->getHotelEarnings(15);
        $revenueByLocation = $this->getRevenueByLocation(10);
        $revenueByRoomType = $this->getRevenueByRoomType(10);
        $topHotels = $this->getTopHotels(5);
        $topLocations = $this->getTopLocations(5);
        $domainPerformance = $this->getDomainPerformance(10);
        $bookingsByDay = $this->getBookingsByDayOfWeek();
        $guestNationality = $this->getGuestNationalityStats(10);
        $paymentStats = $this->getPaymentStats();
        $hourlyData = $this->getHourlyBookingPattern(30);
        $recentBookings = $this->getRecentBookings(10);
        $recentReviews = $this->getRecentReviews(5);

        return array_merge(
            $overview,
            $todayStats,
            [
                // View variables: weekRevenue, weekBookings, monthBookings, monthRevenue, ytdRevenue
                'weekRevenue' => $periodStats['weekRevenue'],
                'weekBookings' => $periodStats['weekBookings'],
                'monthBookings' => $periodStats['monthBookings'],
                'monthRevenue' => $periodStats['monthRevenue'],
                'ytdRevenue' => $periodStats['ytdRevenue'],
            ],
            $growth,
            $averages,
            $pending,
            // View variable: bookingsByStatus
            ['bookingsByStatus' => $statusBreakdown],
            // View variables: revenueChart30, bookingsChart30
            $revenueCharts,
            // View variable: monthlyRevenue
            ['monthlyRevenue' => $monthlyRevenue],
            // View variables: revenueByDomain, hotelEarnings, revenueByLocation, revenueByRoomType
            [
                'revenueByDomain' => $revenueByDomain,
                'hotelEarnings' => $hotelEarnings,
                'revenueByLocation' => $revenueByLocation,
                'revenueByRoomType' => $revenueByRoomType,
            ],
            // View variables: topHotels, topLocations, domainPerformance
            [
                'topHotels' => $topHotels,
                'topLocations' => $topLocations,
                'domainPerformance' => $domainPerformance,
            ],
            // View variables: bookingsByDay, guestNationality, paymentStats, hourlyData
            [
                'bookingsByDay' => $bookingsByDay,
                'guestNationality' => $guestNationality,
                'paymentStats' => $paymentStats,
                'hourlyData' => $hourlyData,
            ],
            // View variables: recentBookings, recentReviews
            [
                'recentBookings' => $recentBookings,
                'recentReviews' => $recentReviews,
            ],
        );
    }

    /**
     * Core counts for the overview cards.
     *
     * View variables: totalHotels, totalBookings, totalRevenue, activeDomains,
     *                 totalLocations, totalReviews, totalRoomTypes, totalUsers
     */
    public function getOverviewMetrics(): array
    {
        return [
            'totalHotels' => Hotel::count(),
            'totalBookings' => Booking::count(),
            'totalRevenue' => Booking::whereIn('status', ['confirmed', 'paid'])->sum('total_amount'),
            'activeDomains' => Domain::where('is_active', true)->count(),
            'totalLocations' => Location::where('is_active', true)->count(),
            'totalReviews' => Review::where('is_approved', true)->count(),
            'totalRoomTypes' => RoomType::count(),
            'totalUsers' => User::count(),
        ];
    }

    /**
     * Today's revenue and bookings, plus yesterday's for comparison.
     *
     * View variables: todayRevenue, todayBookings
     * (yesterdayRevenue and yesterdayBookings are used internally for growth calculations)
     */
    public function getTodayStats(): array
    {
        $today = Carbon::now()->startOfDay();

        $todayRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $today)->sum('total_amount');
        $todayBookings = Booking::where('created_at', '>=', $today)->count();
        $yesterdayRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->whereBetween('created_at', [$today->copy()->subDay(), $today])->sum('total_amount');
        $yesterdayBookings = Booking::whereBetween('created_at', [$today->copy()->subDay(), $today])->count();

        return [
            'todayRevenue' => $todayRevenue,
            'todayBookings' => $todayBookings,
            'yesterdayRevenue' => $yesterdayRevenue,
            'yesterdayBookings' => $yesterdayBookings,
        ];
    }

    /**
     * Week, month, year-to-date and last-month stats.
     *
     * View variables: weekRevenue, weekBookings, monthBookings, monthRevenue, ytdRevenue
     * (lastMonthBookings and lastMonthRevenue are used internally for growth calculations)
     */
    public function getPeriodStats(): array
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $lastMonth = $now->copy()->subMonth();

        $weekRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $weekBookings = Booking::where('created_at', '>=', $startOfWeek)->count();

        $monthBookings = Booking::where('created_at', '>=', $startOfMonth)->count();
        $monthRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfMonth)->sum('total_amount');

        $ytdRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('created_at', '>=', $startOfYear)->sum('total_amount');

        $lastMonthBookings = Booking::whereBetween('created_at', [
            $lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth(),
        ])->count();
        $lastMonthRevenue = Booking::whereIn('status', ['confirmed', 'paid'])
            ->whereBetween('created_at', [
                $lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth(),
            ])->sum('total_amount');

        return [
            'weekRevenue' => $weekRevenue,
            'weekBookings' => $weekBookings,
            'monthBookings' => $monthBookings,
            'monthRevenue' => $monthRevenue,
            'ytdRevenue' => $ytdRevenue,
            'lastMonthBookings' => $lastMonthBookings,
            'lastMonthRevenue' => $lastMonthRevenue,
        ];
    }

    /**
     * Calculate growth percentages from period and today stats.
     *
     * View variables: bookingGrowth, revenueGrowth, todayRevenueGrowth
     */
    public function getGrowthMetrics(array $periodStats = [], array $todayStats = []): array
    {
        if (empty($periodStats)) {
            $periodStats = $this->getPeriodStats();
        }
        if (empty($todayStats)) {
            $todayStats = $this->getTodayStats();
        }

        $monthBookings = $periodStats['monthBookings'];
        $lastMonthBookings = $periodStats['lastMonthBookings'];
        $monthRevenue = $periodStats['monthRevenue'];
        $lastMonthRevenue = $periodStats['lastMonthRevenue'];
        $todayRevenue = $todayStats['todayRevenue'];
        $yesterdayRevenue = $todayStats['yesterdayRevenue'];

        $bookingGrowth = $lastMonthBookings > 0
            ? round((($monthBookings - $lastMonthBookings) / $lastMonthBookings) * 100, 1)
            : ($monthBookings > 0 ? 100 : 0);

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($monthRevenue > 0 ? 100 : 0);

        $todayRevenueGrowth = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        return [
            'bookingGrowth' => $bookingGrowth,
            'revenueGrowth' => $revenueGrowth,
            'todayRevenueGrowth' => $todayRevenueGrowth,
        ];
    }

    /**
     * Revenue growth percentage (month over month).
     *
     * View variable: revenueGrowth
     */
    public function getRevenueGrowth(): float
    {
        $metrics = $this->getGrowthMetrics();

        return $metrics['revenueGrowth'];
    }

    /**
     * Average booking value, average nights stay, and cancellation rate.
     *
     * View variables: avgBookingValue, avgNightsStay, cancellationRate
     */
    public function getAverageMetrics(): array
    {
        $avgBookingValue = Booking::whereIn('status', ['confirmed', 'paid'])->avg('total_amount') ?? 0;
        $avgNightsStay = Booking::whereIn('status', ['confirmed', 'paid'])->avg('num_nights') ?? 0;

        $totalBookings = Booking::count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0;

        return [
            'avgBookingValue' => $avgBookingValue,
            'avgNightsStay' => $avgNightsStay,
            'cancellationRate' => $cancellationRate,
        ];
    }

    /**
     * Counts of items needing attention.
     *
     * View variables: pendingBookings, pendingReviews
     */
    public function getPendingActions(): array
    {
        return [
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'pendingReviews' => Review::where('is_approved', false)->count(),
        ];
    }

    /**
     * Booking counts grouped by status.
     *
     * View variable: bookingsByStatus
     */
    public function getBookingStatusBreakdown(): array
    {
        return Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Daily revenue and booking counts for chart data.
     *
     * View variables: revenueChart30, bookingsChart30
     *
     * @param  int  $days  Number of days to look back (default 30)
     */
    public function getRevenueCharts(int $days = 30): array
    {
        $now = Carbon::now();
        $revenueChart30 = [];
        $bookingsChart30 = [];

        for ($i = $days - 1; $i >= 0; $i--) {
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

        return [
            'revenueChart30' => $revenueChart30,
            'bookingsChart30' => $bookingsChart30,
        ];
    }

    /**
     * Monthly revenue and booking trend for the last N months.
     *
     * View variable: monthlyRevenue
     *
     * @param  int  $months  Number of months to look back (default 12)
     */
    public function getMonthlyRevenueTrend(int $months = 12): array
    {
        $now = Carbon::now();
        $monthlyRevenue = [];

        for ($i = $months - 1; $i >= 0; $i--) {
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

        return $monthlyRevenue;
    }

    /**
     * Top domains ranked by revenue.
     *
     * View variable: revenueByDomain
     *
     * @param  int  $limit  Number of domains to return (default 10)
     */
    public function getTopDomainsByRevenue(int $limit = 10): Collection
    {
        return Domain::select('domains.id', 'domains.name', 'domains.domain')
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
            ->take($limit)
            ->get();
    }

    /**
     * Top hotels ranked by revenue with booking stats.
     *
     * View variable: hotelEarnings
     *
     * @param  int  $limit  Number of hotels to return (default 15)
     */
    public function getHotelEarnings(int $limit = 15): Collection
    {
        return Hotel::select('hotels.id', 'hotels.name', 'hotels.star_rating', 'hotels.location_id', 'hotels.avg_rating', 'hotels.is_active')
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
            ->take($limit)
            ->get();
    }

    /**
     * Revenue breakdown by location.
     *
     * View variable: revenueByLocation
     *
     * @param  int  $limit  Number of locations to return (default 10)
     */
    public function getRevenueByLocation(int $limit = 10): Collection
    {
        return Location::select('locations.id', 'locations.name')
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
            ->take($limit)
            ->get();
    }

    /**
     * Revenue breakdown by room type.
     *
     * View variable: revenueByRoomType
     *
     * @param  int  $limit  Number of room types to return (default 10)
     */
    public function getRevenueByRoomType(int $limit = 10): Collection
    {
        return RoomType::select('room_types.id', 'room_types.name')
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
            ->take($limit)
            ->get();
    }

    /**
     * Top hotels ranked by booking count.
     *
     * View variable: topHotels
     *
     * @param  int  $limit  Number of hotels to return (default 5)
     */
    public function getTopHotels(int $limit = 5): Collection
    {
        return Hotel::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take($limit)
            ->get();
    }

    /**
     * Top locations ranked by hotel count.
     *
     * View variable: topLocations
     *
     * @param  int  $limit  Number of locations to return (default 5)
     */
    public function getTopLocations(int $limit = 5): Collection
    {
        return Location::withCount('hotels')
            ->where('is_active', true)
            ->orderByDesc('hotels_count')
            ->take($limit)
            ->get();
    }

    /**
     * Domain performance table with revenue calculated per domain.
     *
     * View variable: domainPerformance
     *
     * @param  int  $limit  Number of domains to return (default 10)
     */
    public function getDomainPerformance(int $limit = 10): \Illuminate\Support\Collection
    {
        return Domain::where('is_active', true)
            ->withCount(['bookings', 'hotels'])
            ->get()
            ->each(function ($domain) {
                $domain->domain_revenue = Booking::where('domain_id', $domain->id)
                    ->whereIn('status', ['confirmed', 'paid'])
                    ->sum('total_amount');
            })
            ->sortByDesc('domain_revenue')
            ->take($limit)
            ->values();
    }

    /**
     * Booking counts by day of week.
     *
     * View variable: bookingsByDay
     *
     * @return array Array of {label, value} entries for Sun-Sat
     */
    public function getBookingsByDayOfWeek(): array
    {
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

        return $bookingsByDay;
    }

    /**
     * Guest nationality breakdown.
     *
     * View variable: guestNationality
     *
     * @param  int  $limit  Number of nationalities to return (default 10)
     */
    public function getGuestNationalityStats(int $limit = 10): array
    {
        return Booking::select('guest_nationality', DB::raw('count(*) as count'))
            ->whereNotNull('guest_nationality')
            ->where('guest_nationality', '!=', '')
            ->groupBy('guest_nationality')
            ->orderByDesc('count')
            ->take($limit)
            ->pluck('count', 'guest_nationality')
            ->toArray();
    }

    /**
     * Payment status breakdown with counts and totals.
     *
     * View variable: paymentStats
     */
    public function getPaymentStats(): \Illuminate\Support\Collection
    {
        return Payment::select('status', DB::raw('count(*) as count'), DB::raw('COALESCE(SUM(amount), 0) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    /**
     * Hourly booking pattern over the last N days.
     *
     * View variable: hourlyData
     *
     * @param  int  $days  Number of days to look back (default 30)
     * @return array Array of {label, value} entries for each hour 00:00-23:00
     */
    public function getHourlyBookingPattern(int $days = 30): array
    {
        $now = Carbon::now();

        $hourlyBookings = Booking::selectRaw('HOUR(created_at) as hour, count(*) as count')
            ->where('created_at', '>=', $now->copy()->subDays($days))
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

        return $hourlyData;
    }

    /**
     * Recent bookings with hotel and domain eager-loaded.
     *
     * View variable: recentBookings
     *
     * @param  int  $limit  Number of bookings to return (default 10)
     */
    public function getRecentBookings(int $limit = 10): Collection
    {
        return Booking::with(['hotel', 'domain'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Recent reviews with hotel eager-loaded.
     *
     * View variable: recentReviews
     *
     * @param  int  $limit  Number of reviews to return (default 5)
     */
    public function getRecentReviews(int $limit = 5): Collection
    {
        return Review::with('hotel')
            ->latest()
            ->take($limit)
            ->get();
    }
}
