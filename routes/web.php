<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect root to admin
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/admin/dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Domains
    Route::resource('domains', Admin\DomainController::class);
    Route::get('domains/{domain}/hero-slides', [Admin\DomainController::class, 'heroSlides'])->name('domains.hero-slides.index');
    Route::post('domains/{domain}/hero-slides', [Admin\DomainController::class, 'storeHeroSlide'])->name('domains.hero-slides.store');
    Route::put('domains/{domain}/hero-slides/{slide}', [Admin\DomainController::class, 'updateHeroSlide'])->name('domains.hero-slides.update');
    Route::delete('domains/{domain}/hero-slides/{slide}', [Admin\DomainController::class, 'destroyHeroSlide'])->name('domains.hero-slides.destroy');
    Route::post('domains/{domain}/hero-slides/reorder', [Admin\DomainController::class, 'reorderHeroSlides'])->name('domains.hero-slides.reorder');

    // Locations
    Route::resource('locations', Admin\LocationController::class);

    // Hotels
    Route::resource('hotels', Admin\HotelController::class);
    Route::post('hotels/{hotel}/images', [Admin\HotelController::class, 'uploadImages'])->name('hotels.images.store');
    Route::put('hotels/{hotel}/images/{image}', [Admin\HotelController::class, 'updateImage'])->name('hotels.images.update');
    Route::post('hotels/{hotel}/images/{image}/primary', [Admin\HotelController::class, 'setPrimary'])->name('hotels.images.primary');
    Route::delete('hotels/{hotel}/images/{image}', [Admin\HotelController::class, 'deleteImage'])->name('hotels.images.destroy');

    // Room Types (nested under hotels)
    Route::resource('hotels.room-types', Admin\RoomTypeController::class)->shallow();
    Route::delete('room-types/{room_type}/images/{image}', [Admin\RoomTypeController::class, 'destroyImage'])->name('room-types.images.destroy');

    // Amenities
    Route::resource('amenities', Admin\AmenityController::class);

    // Pricing Rules
    Route::resource('pricing-rules', Admin\PricingRuleController::class);

    // Deals
    Route::resource('deals', Admin\DealController::class);

    // Careers
    Route::resource('careers', Admin\CareerController::class);
    Route::resource('career-applications', Admin\CareerApplicationController::class)->only(['index', 'show', 'update']);

    // Blog
    Route::resource('blog-categories', Admin\BlogCategoryController::class);
    Route::resource('blog-posts', Admin\BlogPostController::class);

    // Bookings
    Route::resource('bookings', Admin\BookingController::class)->only(['index', 'show', 'update']);
    Route::post('bookings/{booking}/refund', [Admin\BookingController::class, 'refund'])->name('bookings.refund');
    Route::post('bookings/{booking}/cash-payment', [Admin\BookingController::class, 'markCashPaid'])->name('bookings.cash-payment');
    Route::get('bookings-export', [Admin\BookingController::class, 'export'])->name('bookings.export');

    // Users
    Route::resource('users', Admin\UserController::class);

    // Roles & Permissions
    Route::resource('roles', Admin\RoleController::class);

    // Reviews
    Route::resource('reviews', Admin\ReviewController::class);

    // Testimonials
    Route::get('testimonials', [Admin\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('testimonials/search-reviews', [Admin\TestimonialController::class, 'searchReviews'])->name('testimonials.search-reviews');
    Route::post('testimonials', [Admin\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::delete('testimonials/{id}', [Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    // Contact Inquiries
    Route::resource('contact-inquiries', Admin\ContactInquiryController::class)->only(['index', 'show', 'update']);

    // Customers
    Route::resource('customers', Admin\CustomerController::class)->only(['index', 'show', 'update']);

    // Analytics
    Route::get('analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics');

    // Documentation
    Route::view('documentation', 'admin.documentation.index')->name('documentation');

    // Career Applications API (admin)
    Route::prefix('api/career-applications')->name('api.career-applications.')->group(function () {
        Route::get('/', [Admin\CareerApplicationController::class, 'apiIndex'])->name('index');
        Route::get('{careerApplication}', [Admin\CareerApplicationController::class, 'apiShow'])->name('show');
        Route::put('{careerApplication}/status', [Admin\CareerApplicationController::class, 'apiUpdateStatus'])->name('update-status');
    });

    // API Search (Select2 AJAX)
    Route::prefix('api/search')->name('api.search.')->group(function () {
        Route::get('domains', [Admin\SearchController::class, 'domains'])->name('domains');
        Route::get('locations', [Admin\SearchController::class, 'locations'])->name('locations');
        Route::get('hotels', [Admin\SearchController::class, 'hotels'])->name('hotels');
        Route::get('room-types', [Admin\SearchController::class, 'roomTypes'])->name('room-types');
        Route::get('users', [Admin\SearchController::class, 'users'])->name('users');
    });
});

require __DIR__.'/auth.php';
