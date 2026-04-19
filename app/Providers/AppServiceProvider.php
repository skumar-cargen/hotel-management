<?php

namespace App\Providers;

use App\Enums\BookingStatus;
use App\Enums\CareerApplicationStatus;
use App\Models\Booking;
use App\Models\CareerApplication;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\PricingRule;
use App\Models\Review;
use App\Observers\BookingObserver;
use App\Observers\HotelObserver;
use App\Policies\BookingPolicy;
use App\Policies\DomainPolicy;
use App\Policies\HotelPolicy;
use App\Policies\LocationPolicy;
use App\Policies\PricingRulePolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        Booking::observe(BookingObserver::class);
        Hotel::observe(HotelObserver::class);

        // Register authorization policies
        Gate::policy(Hotel::class, HotelPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Domain::class, DomainPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(PricingRule::class, PricingRulePolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Share pending counts with all admin views (sidebar badges + notification bell)
        View::composer(['components.admin-layout', 'layouts.sidebar'], function ($view) {
            $view->with([
                'pendingBookings' => Booking::where('status', BookingStatus::Pending)->count(),
                'pendingReviews' => Review::where('is_approved', false)->count(),
                'newApplications' => CareerApplication::where('status', CareerApplicationStatus::Received)->count(),
            ]);
        });
    }
}
