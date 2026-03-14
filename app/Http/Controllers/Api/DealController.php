<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class DealController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $domain = $this->domain();

        $query = Deal::active()
            ->current()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id));

        if ($request->filled('hotel')) {
            $query->whereHas('hotels', fn ($q) => $q->where('slug', $request->hotel));
        }

        $deals = $query->get();

        return $this->successResponse(DealResource::collection($deals));
    }

    public function show(string $slug)
    {
        $domain = $this->domain();

        $deal = Deal::active()
            ->current()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where('slug', $slug)
            ->with(['hotels' => fn ($q) => $q->where('is_active', true)->with(['images', 'location'])])
            ->first();

        if (! $deal) {
            return $this->errorResponse('Deal not found.', 404);
        }

        return $this->successResponse(new DealResource($deal));
    }
}
