<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateBookingRequest;
use App\Http\Resources\BookingConfirmationResource;
use App\Http\Resources\BookingSummaryResource;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\MashreqPaymentService;
use App\Services\PricingService;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class BookingController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected PricingService $pricingService,
        protected MashreqPaymentService $paymentService,
    ) {}

    public function store(CreateBookingRequest $request)
    {
        $domain = $this->domain();
        $validated = $request->validated();

        $hotel = Hotel::forDomain($domain->id)->active()->findOrFail($validated['hotel_id']);
        $roomType = RoomType::where('hotel_id', $hotel->id)->active()->findOrFail($validated['room_type_id']);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $numRooms = $validated['num_rooms'];

        // Calculate pricing
        $breakdown = $this->pricingService->calculate($roomType, $checkIn, $checkOut, $numRooms, $domain);

        // Generate reference number
        $reference = 'BK-'.strtoupper(Str::random(8));
        while (Booking::where('reference_number', $reference)->exists()) {
            $reference = 'BK-'.strtoupper(Str::random(8));
        }

        // Optionally attach authenticated customer
        $customerId = null;
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            $accessToken = PersonalAccessToken::findToken($bearerToken);
            if ($accessToken && $accessToken->tokenable instanceof Customer && $accessToken->tokenable->is_active) {
                $customerId = $accessToken->tokenable->id;
            }
        }

        $booking = Booking::create([
            'reference_number' => $reference,
            'domain_id' => $domain->id,
            'customer_id' => $customerId,
            'hotel_id' => $hotel->id,
            'room_type_id' => $roomType->id,
            'guest_first_name' => $validated['guest_first_name'],
            'guest_last_name' => $validated['guest_last_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'guest_nationality' => $validated['guest_nationality'] ?? null,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'num_nights' => $breakdown->numNights,
            'num_adults' => $validated['num_adults'],
            'num_children' => $validated['num_children'] ?? 0,
            'num_rooms' => $numRooms,
            'special_requests' => $validated['special_requests'] ?? null,
            'room_price_per_night' => $breakdown->finalPerNight,
            'subtotal' => $breakdown->subtotal,
            'tax_amount' => $breakdown->taxAmount,
            'tax_percentage' => $breakdown->taxPercentage,
            'tourism_fee' => $breakdown->tourismFee,
            'service_charge' => $breakdown->serviceCharge,
            'total_amount' => $breakdown->totalAmount,
            'currency' => 'AED',
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'booked_at' => now(),
        ]);

        $booking->load(['hotel', 'roomType']);

        return $this->successResponse(new BookingSummaryResource($booking), 201);
    }

    public function show(string $reference)
    {
        $domain = $this->domain();

        $booking = Booking::where('reference_number', $reference)
            ->where('domain_id', $domain->id)
            ->with(['hotel', 'roomType'])
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        return $this->successResponse(new BookingSummaryResource($booking));
    }

    public function initiatePayment(string $reference)
    {
        $domain = $this->domain();

        $booking = Booking::where('reference_number', $reference)
            ->where('domain_id', $domain->id)
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        if ($booking->status !== 'pending') {
            return $this->errorResponse('Booking is not in a payable state.', 422);
        }

        $result = $this->paymentService->initiatePayment($booking);

        if (! $result['success']) {
            return $this->errorResponse($result['error'] ?? 'Payment initiation failed.', 422);
        }

        return $this->successResponse([
            'payment_id' => $result['payment_id'],
            'redirect_url' => $result['redirect_url'],
        ]);
    }

    public function confirmation(string $reference)
    {
        $domain = $this->domain();

        $booking = Booking::where('reference_number', $reference)
            ->where('domain_id', $domain->id)
            ->with(['hotel', 'roomType', 'payments'])
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        return $this->successResponse(new BookingConfirmationResource($booking));
    }

    public function cancel(string $reference)
    {
        $domain = $this->domain();

        $booking = Booking::where('reference_number', $reference)
            ->where('domain_id', $domain->id)
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        $guestEmail = request()->input('guest_email');
        if (! $guestEmail || $guestEmail !== $booking->guest_email) {
            return $this->errorResponse('Email verification failed.', 403);
        }

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return $this->errorResponse('This booking cannot be cancelled.', 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => request()->input('cancellation_reason'),
            'cancelled_at' => now(),
        ]);

        return $this->successResponse([
            'message' => 'Booking has been cancelled successfully.',
            'reference_number' => $booking->reference_number,
        ]);
    }
}
