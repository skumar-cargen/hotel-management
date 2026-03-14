<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DomainConfigResource;
use App\Traits\ApiResponses;

class DomainConfigController extends Controller
{
    use ApiResponses;

    public function show()
    {
        $domain = $this->domain();
        $domain->load(['heroSlides', 'locations' => function ($q) {
            $q->where('locations.is_active', true)->withCount('hotels')->latest('locations.created_at')->take(5);
        }]);

        return $this->successResponse(new DomainConfigResource($domain));
    }
}
