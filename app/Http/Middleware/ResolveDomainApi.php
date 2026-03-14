<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolveDomainApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->header('X-Domain');

        if (! $slug) {
            return response()->json([
                'success' => false,
                'message' => 'X-Domain header is required.',
            ], 400);
        }

        $domain = Cache::remember("domain_api:{$slug}", 3600, function () use ($slug) {
            return Domain::where('slug', $slug)->where('is_active', true)->first();
        });

        if (! $domain) {
            return response()->json([
                'success' => false,
                'message' => 'Domain not found or inactive.',
            ], 404);
        }

        $request->attributes->set('domain', $domain);

        return $next($request);
    }
}
