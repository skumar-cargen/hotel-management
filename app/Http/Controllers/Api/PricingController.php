<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculatePriceRequest;
use App\Http\Resources\PriceBreakdownResource;
use App\Models\RoomType;
use App\Services\PricingService;
use App\Traits\ApiResponses;
use Carbon\Carbon;

class PricingController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected PricingService $pricingService,
    ) {}

    public function calculate(CalculatePriceRequest $request)
    {
        $domain = $this->domain();
        $roomType = RoomType::with('hotel')->findOrFail($request->room_type_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $numRooms = $request->integer('num_rooms', 1);

        $breakdown = $this->pricingService->calculate(
            $roomType,
            $checkIn,
            $checkOut,
            $numRooms,
            $domain,
        );

        return $this->successResponse(new PriceBreakdownResource($breakdown));
    }
}
