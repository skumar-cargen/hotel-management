<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CareerApplicationRequest;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use App\Models\CareerApplication;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $domain = $this->domain();

        $careers = Career::active()
            ->open()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->orderBy('sort_order')
            ->orderBy('last_apply_date')
            ->get();

        return $this->successResponse(CareerResource::collection($careers));
    }

    public function show(string $slug)
    {
        $domain = $this->domain();

        $career = Career::active()
            ->open()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->successResponse(new CareerResource($career));
    }

    public function apply(CareerApplicationRequest $request, string $slug)
    {
        $domain = $this->domain();

        $career = Career::active()
            ->open()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where('slug', $slug)
            ->first();

        if (! $career) {
            return $this->errorResponse('Career listing not found or no longer accepting applications.', 404);
        }

        $validated = $request->validated();

        $resumePath = $request->file('resume')->store('career-applications', 'public');

        CareerApplication::create([
            'career_id' => $career->id,
            'domain_id' => $domain->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'resume_path' => $resumePath,
        ]);

        return $this->successResponse([
            'message' => 'Your application has been submitted successfully. We will review it and get back to you.',
        ], 201);
    }
}
