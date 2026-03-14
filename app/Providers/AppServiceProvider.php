<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\CareerApplication;
use App\Models\Hotel;
use App\Models\Review;
use App\Observers\HotelObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Hotel::observe(HotelObserver::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Share pending counts with all admin views (sidebar badges + notification bell)
        View::composer(['components.admin-layout', 'layouts.sidebar'], function ($view) {
            $view->with([
                'pendingBookings' => Booking::where('status', 'pending')->count(),
                'pendingReviews' => Review::where('is_approved', false)->count(),
                'newApplications' => CareerApplication::where('status', 'new')->count(),
            ]);
        });
    }
}
