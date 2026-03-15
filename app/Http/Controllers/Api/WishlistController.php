<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ApiResponses;

    /**
     * List user's wishlist (hotel IDs).
     */
    public function index(Request $request)
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->select('hotel_id')
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse($items);
    }

    /**
     * Add hotel to wishlist.
     */
    public function store(Request $request, int $hotelId)
    {
        Wishlist::firstOrCreate([
            'user_id'  => $request->user()->id,
            'hotel_id' => $hotelId,
        ]);

        return $this->successResponse(['hotel_id' => $hotelId], 201);
    }

    /**
     * Remove hotel from wishlist.
     */
    public function destroy(Request $request, int $hotelId)
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('hotel_id', $hotelId)
            ->delete();

        return $this->successResponse(null);
    }
}
