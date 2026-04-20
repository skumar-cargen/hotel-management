<?php

use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\DomainConfigController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\MpgsController;
// use App\Http\Controllers\Api\PaymentCallbackController; // Temporarily disabled — Mashreq payment
use App\Http\Controllers\Api\PricingController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1/...
|--------------------------------------------------------------------------
|
| All routes require X-Domain header (resolved via resolve.domain.api
| middleware) except the payment callback.
|
*/

// Payment callback — no domain middleware (gateway calls this)
// Temporarily disabled — Mashreq payment
// Route::post('payments/callback', [PaymentCallbackController::class, 'handle'])
//     ->name('api.payment.callback');

// All domain-scoped routes
Route::middleware(['resolve.domain.api', 'throttle:api'])->group(function () {

    // Customer Auth (public)
    Route::prefix('auth')->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1');
        Route::post('login', [CustomerAuthController::class, 'login'])->middleware('throttle:10,1');
        Route::post('google', [CustomerAuthController::class, 'google'])->middleware('throttle:10,1');
        Route::post('forgot-password', [CustomerAuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    });

    // Customer Authenticated
    Route::middleware(['auth:sanctum', 'customer.auth'])->prefix('customer')->group(function () {
        Route::post('auth/logout', [CustomerAuthController::class, 'logout']);
        Route::get('profile', [CustomerProfileController::class, 'show']);
        Route::put('profile', [CustomerProfileController::class, 'update']);
        Route::post('avatar', [CustomerProfileController::class, 'uploadAvatar']);
        Route::put('password', [CustomerProfileController::class, 'changePassword']);
        Route::get('bookings', [CustomerProfileController::class, 'bookings']);
        Route::get('bookings/{reference}', [CustomerProfileController::class, 'bookingDetail']);
        Route::delete('account', [CustomerProfileController::class, 'deleteAccount']);

        // Wishlist
        Route::get('wishlist', [WishlistController::class, 'index']);
        Route::post('wishlist/{hotelId}', [WishlistController::class, 'store']);
        Route::delete('wishlist/{hotelId}', [WishlistController::class, 'destroy']);
    });

    // Domain config
    Route::get('domain/config', [DomainConfigController::class, 'show']);

    // Hotels
    Route::get('hotels', [HotelController::class, 'index']);
    Route::get('hotels/search', [HotelController::class, 'search']);
    Route::get('hotels/{slug}', [HotelController::class, 'show']);
    Route::get('hotels/{slug}/reviews', [HotelController::class, 'reviews']);

    // Locations
    Route::get('locations', [LocationController::class, 'index']);
    Route::get('locations/{slug}', [LocationController::class, 'show']);

    // Availability
    Route::get('availability', [AvailabilityController::class, 'check']);

    // Pricing
    Route::post('pricing/calculate', [PricingController::class, 'calculate'])
        ->middleware('throttle:30,1');

    // Bookings
    Route::post('bookings', [BookingController::class, 'store'])
        ->middleware('throttle:10,1');
    Route::get('bookings/{reference}', [BookingController::class, 'show']);
    // Temporarily disabled — Mashreq payment
    // Route::post('bookings/{reference}/pay', [BookingController::class, 'initiatePayment'])
    //     ->middleware('throttle:5,1');
    Route::get('bookings/{reference}/confirmation', [BookingController::class, 'confirmation']);

    // MPGS Payments
    Route::post('payments/create-session', [MpgsController::class, 'createSession'])
        ->middleware('throttle:5,1');
    Route::post('payments/verify', [MpgsController::class, 'verify'])
        ->middleware('throttle:5,1');

    // Amenities
    Route::get('amenities', [AmenityController::class, 'index']);

    // Deals
    Route::get('deals', [DealController::class, 'index']);
    Route::get('deals/{slug}', [DealController::class, 'show']);

    // Testimonials
    Route::get('testimonials', [TestimonialController::class, 'index']);

    // Careers
    Route::get('careers', [CareerController::class, 'index']);
    Route::get('careers/{slug}', [CareerController::class, 'show']);
    Route::post('careers/{slug}/apply', [CareerController::class, 'apply'])
        ->middleware('throttle:5,1');

    // Blog
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('blog/categories', [BlogController::class, 'categories']);
    Route::get('blog/posts', [BlogController::class, 'index']);
    Route::get('blog/posts/featured', [BlogController::class, 'featured']);
    Route::get('blog/posts/{slug}', [BlogController::class, 'show']);
    Route::get('blog/categories/{slug}', [BlogController::class, 'categoryPosts']);
    Route::get('blog/{slug}', [BlogController::class, 'show']);

    // Content Pages
    Route::get('pages/{slug}', [PageController::class, 'show']);

    // Review submission
    Route::post('hotels/{slug}/reviews', [HotelController::class, 'submitReview'])
        ->middleware('throttle:5,1');

    // Review helpful toggle
    Route::post('reviews/{reviewId}/helpful', [HotelController::class, 'toggleReviewHelpful'])
        ->middleware('throttle:30,1');

    // Contact form
    Route::post('contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,1');

    // Newsletter
    Route::post('newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:10,1');
    Route::post('newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);

    // Search autocomplete
    Route::get('search/suggestions', [SearchController::class, 'suggestions']);

    // Booking cancellation
    Route::post('bookings/{reference}/cancel', [BookingController::class, 'cancel'])
        ->middleware('throttle:5,1');
});
