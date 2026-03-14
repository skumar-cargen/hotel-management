<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Location;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class HotelController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Hotel::query()->with('location')->withCount(['roomTypes', 'reviews']);
            $this->scopeHotelsForUser($query);

            return DataTables::of($query)
                ->addColumn('location_name', function ($hotel) {
                    return $hotel->location->name ?? '-';
                })
                ->addColumn('stars', function ($hotel) {
                    $stars = '';
                    for ($i = 0; $i < $hotel->star_rating; $i++) {
                        $stars .= '<i class="bx bxs-star text-warning"></i>';
                    }

                    return $stars;
                })
                ->addColumn('status', function ($hotel) {
                    return $hotel->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($hotel) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.hotels.edit', $hotel).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="'.route('admin.hotels.room-types.index', $hotel).'"><i class="bx bx-bed me-2"></i>Room Types</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.hotels.destroy', $hotel).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['stars', 'status', 'action'])
                ->make(true);
        }

        $locations = Location::active()->orderBy('name')->get();

        return view('admin.hotels.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.hotels.edit', [
            'hotel' => new Hotel,
            'locations' => Location::active()->get(),
            'amenities' => Amenity::where('is_active', true)->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'),
            'domains' => $this->userDomains(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'star_rating' => 'required|integer|between:1,5',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'check_in_time' => 'nullable|string|max:10',
            'check_out_time' => 'nullable|string|max:10',
            'cancellation_policy' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'faq_data' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_beach_access'] = $request->boolean('is_beach_access', false);
        $validated['is_family_friendly'] = $request->boolean('is_family_friendly', false);

        $hotel = Hotel::create($validated);

        if ($request->has('amenities')) {
            $hotel->amenities()->sync($request->input('amenities', []));
        }

        // For domain managers: auto-assign their domains if none selected
        $domainIds = $request->input('domains', []);
        if (empty($domainIds) && Auth::user()->isDomainManager()) {
            $domainIds = Auth::user()->managedDomainIds();
        }
        $hotel->domains()->sync($domainIds);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function show(Hotel $hotel)
    {
        $this->authorizeHotel($hotel);
        $hotel->load(['location', 'amenities', 'roomTypes', 'images', 'domains']);

        return view('admin.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel)
    {
        $this->authorizeHotel($hotel);
        $hotel->load(['amenities', 'domains', 'images']);
        $locations = Location::active()->orderBy('name')->get();
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        $domains = $this->userDomains();
        $selectedAmenities = $hotel->amenities->pluck('id')->toArray();
        $selectedDomains = $hotel->domains->pluck('id')->toArray();
        $imageCategories = HotelImage::CATEGORIES;

        return view('admin.hotels.edit', compact('hotel', 'locations', 'amenities', 'domains', 'selectedAmenities', 'selectedDomains', 'imageCategories'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $this->authorizeHotel($hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'star_rating' => 'required|integer|between:1,5',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'check_in_time' => 'nullable|string|max:10',
            'check_out_time' => 'nullable|string|max:10',
            'cancellation_policy' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'faq_data' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_beach_access'] = $request->boolean('is_beach_access');
        $validated['is_family_friendly'] = $request->boolean('is_family_friendly');

        $hotel->update($validated);
        $hotel->amenities()->sync($request->input('amenities', []));
        $hotel->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel)
    {
        $this->authorizeHotel($hotel);
        $hotel->delete();

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully.');
    }

    public function uploadImages(Request $request, Hotel $hotel)
    {
        $this->authorizeHotel($hotel);

        $request->validate([
            'images.*' => 'required|image|max:5120',
            'category' => 'nullable|string|in:'.implode(',', array_keys(HotelImage::CATEGORIES)),
        ]);

        $category = $request->input('category', 'general');
        $uploaded = [];

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('hotels/'.$hotel->id, 'public');
            $image = HotelImage::create([
                'hotel_id' => $hotel->id,
                'category' => $category,
                'image_path' => $path,
                'alt_text' => $hotel->name,
                'is_primary' => $hotel->images()->count() === 0,
            ]);
            $uploaded[] = $image;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'images' => collect($uploaded)->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => asset('storage/'.$img->image_path),
                    'category' => $img->category,
                    'category_label' => HotelImage::CATEGORIES[$img->category] ?? $img->category,
                    'alt_text' => $img->alt_text,
                    'is_primary' => $img->is_primary,
                ])->values(),
            ]);
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function deleteImage(Request $request, Hotel $hotel, HotelImage $image)
    {
        $this->authorizeHotel($hotel);
        $image->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    public function setPrimary(Hotel $hotel, HotelImage $image)
    {
        $this->authorizeHotel($hotel);

        $hotel->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    public function updateImage(Request $request, Hotel $hotel, HotelImage $image)
    {
        $this->authorizeHotel($hotel);

        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:'.implode(',', array_keys(HotelImage::CATEGORIES)),
        ]);

        $image->update($validated);

        return response()->json([
            'success' => true,
            'image' => [
                'id' => $image->id,
                'alt_text' => $image->alt_text,
                'caption' => $image->caption,
                'category' => $image->category,
                'category_label' => HotelImage::CATEGORIES[$image->category] ?? $image->category,
            ],
        ]);
    }
}
