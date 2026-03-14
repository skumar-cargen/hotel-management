<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubmitReviewRequest;
use App\Http\Resources\HotelDetailResource;
use App\Http\Resources\HotelListResource;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use App\Services\PricingService;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected PricingService $pricingService,
    ) {}

    public function index(Request $request)
    {
        $domain = $this->domain();

        $query = Hotel::forDomain($domain->id)
            ->active()
            ->with([
                'images',
                'location',
                'deals' => fn ($q) => $q->active()->current()->whereHas('domains', fn ($dq) => $dq->where('domains.id', $domain->id)),
            ])
            ->when($request->filled('location'), fn ($q) => $q->whereHas('location', fn ($lq) => $lq->where('slug', $request->location)))
            ->when($request->filled('star_rating'), fn ($q) => $q->where('star_rating', $request->star_rating))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->when($request->filled('sort'), function ($q) use ($request) {
                return match ($request->sort) {
                    'price_asc' => $q->orderBy('min_price', 'asc'),
                    'price_desc' => $q->orderBy('min_price', 'desc'),
                    'rating' => $q->orderBy('avg_rating', 'desc'),
                    'name' => $q->orderBy('name', 'asc'),
                    default => $q->orderBy('sort_order'),
                };
            }, fn ($q) => $q->orderBy('sort_order'));

        $hotels = $query->with('roomTypes')->paginate($request->integer('per_page', 12));

        // Attach domain-aware display price to each hotel
        foreach ($hotels as $hotel) {
            $minDisplayPrice = $hotel->roomTypes
                ->where('is_active', true)
                ->map(fn ($rt) => $this->pricingService->getDisplayPrice($rt, $domain))
                ->min();

            $hotel->display_min_price = $minDisplayPrice ?? (float) $hotel->min_price;
        }

        return $this->paginatedResponse(HotelListResource::collection($hotels));
    }

    public function search(Request $request)
    {
        $domain = $this->domain();

        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $query = $request->input('q');

        $hotels = Hotel::forDomain($domain->id)
            ->active()
            ->with([
                'images',
                'location',
                'deals' => fn ($q) => $q->active()->current()->whereHas('domains', fn ($dq) => $dq->where('domains.id', $domain->id)),
            ])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhereHas('location', fn ($lq) => $lq->where('name', 'like', "%{$query}%")->orWhere('city', 'like', "%{$query}%"));
            })
            ->orderBy('sort_order')
            ->with('roomTypes')
            ->paginate($request->integer('per_page', 12));

        foreach ($hotels as $hotel) {
            $minDisplayPrice = $hotel->roomTypes
                ->where('is_active', true)
                ->map(fn ($rt) => $this->pricingService->getDisplayPrice($rt, $domain))
                ->min();

            $hotel->display_min_price = $minDisplayPrice ?? (float) $hotel->min_price;
        }

        return $this->paginatedResponse(HotelListResource::collection($hotels));
    }

    public function show(string $slug)
    {
        $domain = $this->domain();

        $hotel = Hotel::forDomain($domain->id)
            ->active()
            ->where('slug', $slug)
            ->with([
                'images',
                'amenities' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'roomTypes' => fn ($q) => $q->active()->with(['images', 'amenities' => fn ($aq) => $aq->where('is_active', true)]),
                'location',
                'approvedReviews' => fn ($q) => $q->latest()->limit(5),
                'deals' => fn ($q) => $q->active()->current()->whereHas('domains', fn ($dq) => $dq->where('domains.id', $domain->id)),
            ])
            ->first();

        if (! $hotel) {
            return $this->errorResponse('Hotel not found.', 404);
        }

        // Attach display price to each room type
        foreach ($hotel->roomTypes as $roomType) {
            $roomType->display_price = $this->pricingService->getDisplayPrice($roomType, $domain);
        }

        return $this->successResponse(new HotelDetailResource($hotel));
    }

    public function reviews(Request $request, string $slug)
    {
        $domain = $this->domain();

        $hotel = Hotel::forDomain($domain->id)
            ->active()
            ->where('slug', $slug)
            ->first();

        if (! $hotel) {
            return $this->errorResponse('Hotel not found.', 404);
        }

        $reviews = $hotel->approvedReviews()
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return $this->paginatedResponse(ReviewResource::collection($reviews));
    }

    public function submitReview(SubmitReviewRequest $request, string $slug)
    {
        $domain = $this->domain();

        $hotel = Hotel::forDomain($domain->id)
            ->active()
            ->where('slug', $slug)
            ->first();

        if (! $hotel) {
            return $this->errorResponse('Hotel not found.', 404);
        }

        $validated = $request->validated();

        $bookingId = null;
        if (! empty($validated['booking_reference'])) {
            $booking = Booking::where('reference_number', $validated['booking_reference'])
                ->where('hotel_id', $hotel->id)
                ->where('guest_email', $validated['guest_email'])
                ->first();

            $bookingId = $booking?->id;
        }

        Review::create([
            'hotel_id' => $hotel->id,
            'booking_id' => $bookingId,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_verified' => $bookingId !== null,
            'is_approved' => false,
        ]);

        return $this->successResponse([
            'message' => 'Thank you for your review! It will be visible after moderation.',
        ], 201);
    }
}
