<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckAvailabilityRequest;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityController extends Controller
{
    use ApiResponses;

    public function check(CheckAvailabilityRequest $request)
    {
        $roomType = RoomType::with('hotel')->findOrFail($request->room_type_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $numRooms = $request->integer('num_rooms', 1);

        $period = CarbonPeriod::create($checkIn, '1 day', $checkOut->copy()->subDay());
        $dates = [];
        $available = true;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            $availability = RoomAvailability::where('room_type_id', $roomType->id)
                ->where('date', $dateStr)
                ->first();

            $totalRooms = $roomType->total_rooms;
            $bookedRooms = $availability?->booked_rooms ?? 0;
            $availableRooms = $availability ? $availability->available_rooms - $bookedRooms : $totalRooms;
            $isClosed = $availability?->is_closed ?? false;

            if ($isClosed || $availableRooms < $numRooms) {
                $available = false;
            }

            $dates[] = [
                'date' => $dateStr,
                'available_rooms' => max(0, $availableRooms),
                'is_closed' => $isClosed,
                'has_availability' => ! $isClosed && $availableRooms >= $numRooms,
            ];
        }

        return $this->successResponse([
            'room_type_id' => $roomType->id,
            'room_type_name' => $roomType->name,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'num_rooms' => $numRooms,
            'available' => $available,
            'dates' => $dates,
        ]);
    }
}
