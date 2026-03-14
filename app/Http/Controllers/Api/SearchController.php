<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Location;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponses;

    public function suggestions(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $domain = $this->domain();
        $query = $request->input('q');

        $hotels = Hotel::forDomain($domain->id)
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%");
            })
            ->with(['images'])
            ->limit(5)
            ->get()
            ->map(function ($hotel) {
                $primaryImage = $hotel->images->firstWhere('is_primary', true) ?? $hotel->images->first();

                return [
                    'type' => 'hotel',
                    'name' => $hotel->name,
                    'slug' => $hotel->slug,
                    'image' => $primaryImage ? asset('storage/'.$primaryImage->image_path) : null,
                ];
            });

        $locations = Location::active()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->limit(3)
            ->get()
            ->map(fn ($location) => [
                'type' => 'location',
                'name' => $location->name,
                'slug' => $location->slug,
                'image' => $location->image_path ? asset('storage/'.$location->image_path) : null,
            ]);

        return $this->successResponse([
            'hotels' => $hotels,
            'locations' => $locations,
        ]);
    }
}
