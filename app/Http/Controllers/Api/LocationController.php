<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelListResource;
use App\Http\Resources\LocationResource;
use App\Models\Hotel;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $domain = $this->domain();

        $locations = $domain->locations()
            ->where('domain_location.is_active', true)
            ->where('locations.is_active', true)
            ->withCount(['hotels' => fn ($q) => $q->forDomain($domain->id)->active()])
            ->orderBy('domain_location.sort_order')
            ->get();

        return $this->successResponse(LocationResource::collection($locations));
    }

    public function show(Request $request, string $slug)
    {
        $domain = $this->domain();

        $location = $domain->locations()
            ->where('domain_location.is_active', true)
            ->where('locations.is_active', true)
            ->where('locations.slug', $slug)
            ->withCount(['hotels' => fn ($q) => $q->forDomain($domain->id)->active()])
            ->first();

        if (! $location) {
            return $this->errorResponse('Location not found.', 404);
        }

        $hotels = Hotel::forDomain($domain->id)
            ->active()
            ->where('location_id', $location->id)
            ->with(['images', 'location'])
            ->orderBy('sort_order')
            ->paginate($request->integer('per_page', 12));

        return $this->successResponse([
            'location' => new LocationResource($location),
            'hotels' => HotelListResource::collection($hotels),
        ], meta: [
            'current_page' => $hotels->currentPage(),
            'last_page' => $hotels->lastPage(),
            'per_page' => $hotels->perPage(),
            'total' => $hotels->total(),
        ]);
    }
}
