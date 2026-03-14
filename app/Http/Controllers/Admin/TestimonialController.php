<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Review;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        $domains = $this->userDomains();
        $selectedDomain = null;
        $testimonials = collect();

        if ($request->filled('domain_id')) {
            $selectedDomain = $domains->firstWhere('id', $request->domain_id);
        }

        if (! $selectedDomain && $domains->isNotEmpty()) {
            $selectedDomain = $domains->first();
        }

        if ($selectedDomain) {
            $testimonials = $selectedDomain->testimonials()
                ->with('hotel')
                ->get();
        }

        return view('admin.testimonials.index', compact('domains', 'selectedDomain', 'testimonials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'review_id' => 'required|exists:reviews,id',
        ]);

        $domain = Domain::findOrFail($validated['domain_id']);

        // Check max 5 testimonials per domain
        if ($domain->testimonials()->count() >= 5) {
            return back()->with('error', 'Maximum 5 testimonials allowed per domain. Remove one first.');
        }

        // Check if already added
        if ($domain->testimonials()->where('review_id', $validated['review_id'])->exists()) {
            return back()->with('error', 'This review is already a testimonial for this domain.');
        }

        // Verify review is approved
        $review = Review::findOrFail($validated['review_id']);
        if (! $review->is_approved) {
            return back()->with('error', 'Only approved reviews can be added as testimonials.');
        }

        $nextOrder = $domain->testimonials()->max('domain_testimonials.sort_order') + 1;
        $domain->testimonials()->attach($validated['review_id'], ['sort_order' => $nextOrder]);

        return back()->with('success', 'Review added as testimonial successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $domain = Domain::findOrFail($request->domain_id);
        $domain->testimonials()->detach($id);

        return back()->with('success', 'Testimonial removed successfully.');
    }

    public function searchReviews(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'q' => 'nullable|string|max:255',
        ]);

        $domain = Domain::findOrFail($request->domain_id);

        // Get hotel IDs attached to this domain
        $hotelIds = $domain->hotels()->pluck('hotels.id');

        // Get already-added testimonial review IDs
        $existingIds = $domain->testimonials()->pluck('reviews.id');

        $query = Review::query()
            ->with('hotel')
            ->approved()
            ->whereIn('hotel_id', $hotelIds)
            ->whereNotIn('id', $existingIds);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('hotel', fn ($h) => $h->where('name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->latest()->limit(20)->get();

        return response()->json([
            'reviews' => $reviews->map(fn ($r) => [
                'id' => $r->id,
                'guest_name' => $r->guest_name,
                'rating' => $r->rating,
                'title' => $r->title,
                'comment' => \Illuminate\Support\Str::limit($r->comment, 100),
                'hotel_name' => $r->hotel->name ?? '-',
                'created_at' => $r->created_at?->format('M d, Y'),
            ]),
        ]);
    }
}
