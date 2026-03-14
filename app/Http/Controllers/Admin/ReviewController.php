<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Observers\HotelObserver;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Review::query()->with('hotel');
            $this->scopeReviewsForUser($query);

            return DataTables::of($query)
                ->addColumn('hotel_name', function ($review) {
                    return $review->hotel->name ?? '-';
                })
                ->addColumn('rating_display', function ($review) {
                    $stars = '';
                    for ($i = 0; $i < $review->rating; $i++) {
                        $stars .= '<i class="bx bxs-star text-warning"></i>';
                    }
                    for ($i = $review->rating; $i < 5; $i++) {
                        $stars .= '<i class="bx bx-star text-muted"></i>';
                    }

                    return $stars;
                })
                ->addColumn('approval', function ($review) {
                    return $review->is_approved
                        ? '<span class="badge bg-success">Approved</span>'
                        : '<span class="badge bg-warning">Pending</span>';
                })
                ->editColumn('created_at', function ($review) {
                    return $review->created_at ? $review->created_at->format('M d, Y') : '-';
                })
                ->addColumn('action', function ($review) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.reviews.edit', $review).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.reviews.destroy', $review).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['rating_display', 'approval', 'action'])
                ->make(true);
        }

        return view('admin.reviews.index');
    }

    public function show(Review $review)
    {
        $review->load(['hotel', 'booking']);

        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'nullable|string',
        ]);

        $validated['is_approved'] = $request->boolean('is_approved');

        if ($request->filled('admin_reply') && ! $review->replied_at) {
            $validated['replied_at'] = now();
        }

        $review->update($validated);

        HotelObserver::recalculateForHotel($review->hotel);

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        $hotel = $review->hotel;
        $review->delete();
        HotelObserver::recalculateForHotel($hotel);

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
}
