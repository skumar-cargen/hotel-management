<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use App\Observers\HotelObserver;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class RoomTypeController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request, Hotel $hotel)
    {
        $this->authorizeHotel($hotel);

        if ($request->ajax()) {
            $query = $hotel->roomTypes();

            return DataTables::of($query)
                ->addColumn('price_formatted', function ($roomType) {
                    return number_format($roomType->base_price, 2).' AED';
                })
                ->addColumn('status', function ($roomType) {
                    return $roomType->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($roomType) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.room-types.edit', $roomType).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.room-types.destroy', $roomType).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.room-types.index', compact('hotel'));
    }

    public function create(Hotel $hotel)
    {
        $this->authorizeHotel($hotel);

        return view('admin.room-types.edit', [
            'roomType' => new RoomType,
            'hotel' => $hotel,
            'amenities' => Amenity::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'),
            'selectedAmenities' => [],
        ]);
    }

    public function store(Request $request, Hotel $hotel)
    {
        $this->authorizeHotel($hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_guests' => 'required|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'bed_type' => 'nullable|string|max:100',
            'room_size_sqm' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'images.*' => 'image|max:5120',
        ]);

        $validated['hotel_id'] = $hotel->id;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $roomType = RoomType::create($validated);

        if ($request->has('amenities')) {
            $roomType->amenities()->sync($request->input('amenities', []));
        }

        $this->uploadImages($request, $roomType);

        HotelObserver::recalculateForHotel($hotel);

        return redirect()->route('admin.room-types.edit', $roomType)->with('success', 'Room type created successfully.');
    }

    public function show(RoomType $roomType)
    {
        $this->authorizeHotel($roomType->hotel);
        $roomType->load(['hotel', 'amenities', 'images']);

        return view('admin.room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType)
    {
        $this->authorizeHotel($roomType->hotel);
        $roomType->load('images');
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        $selectedAmenities = $roomType->amenities->pluck('id')->toArray();

        return view('admin.room-types.edit', compact('roomType', 'amenities', 'selectedAmenities'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $this->authorizeHotel($roomType->hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_guests' => 'required|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'bed_type' => 'nullable|string|max:100',
            'room_size_sqm' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'images.*' => 'image|max:5120',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $roomType->update($validated);
        $roomType->amenities()->sync($request->input('amenities', []));

        $this->uploadImages($request, $roomType);

        HotelObserver::recalculateForHotel($roomType->hotel);

        return redirect()->route('admin.room-types.edit', $roomType)->with('success', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType)
    {
        $this->authorizeHotel($roomType->hotel);
        $hotel = $roomType->hotel;
        $roomType->delete();
        HotelObserver::recalculateForHotel($hotel);

        return redirect()->route('admin.hotels.room-types.index', $hotel)->with('success', 'Room type deleted successfully.');
    }

    public function destroyImage(RoomType $roomType, RoomTypeImage $image)
    {
        $this->authorizeHotel($roomType->hotel);

        if ($image->room_type_id !== $roomType->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    private function uploadImages(Request $request, RoomType $roomType): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sortOrder = $roomType->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $file) {
            $path = $file->store('room-types/'.$roomType->id, 'public');
            $sortOrder++;

            RoomTypeImage::create([
                'room_type_id' => $roomType->id,
                'image_path' => $path,
                'alt_text' => $roomType->name,
                'is_primary' => $roomType->images()->count() === 0,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
