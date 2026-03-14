<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AmenityResource;
use App\Models\Amenity;
use App\Traits\ApiResponses;

class AmenityController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $amenities = Amenity::active()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $grouped = $amenities->groupBy('category')->map(fn ($items) => AmenityResource::collection($items));

        return $this->successResponse($grouped);
    }
}
