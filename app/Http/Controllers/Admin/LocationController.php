<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Http\Requests\Admin\UpdateLocationRequest;
use App\Models\Domain;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Location::query()->withCount('hotels')->latest();

            return DataTables::of($query)
                ->addColumn('is_featured', function ($location) {
                    return $location->is_featured
                        ? '<span class="badge bg-success">Featured</span>'
                        : '<span class="badge bg-secondary">No</span>';
                })
                ->addColumn('status', function ($location) {
                    return $location->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($location) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.locations.edit', $location).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.locations.destroy', $location).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['is_featured', 'status', 'action'])
                ->make(true);
        }

        return view('admin.locations.index');
    }

    public function create()
    {
        return view('admin.locations.edit', ['location' => new Location, 'domains' => Domain::active()->get()]);
    }

    public function store(StoreLocationRequest $request)
    {
        $validated = $request->validated();

        $slug = Str::slug($validated['name']);
        $count = Location::withTrashed()->where('slug', $slug)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('locations', 'public');
        }

        $location = Location::create($validated);

        if ($request->has('domains')) {
            $location->domains()->sync($request->input('domains', []));
        }

        return redirect()->route('admin.locations.index')->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->load('hotels');

        return view('admin.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $domains = Domain::active()->get();
        $selectedDomains = $location->domains->pluck('id')->toArray();

        return view('admin.locations.edit', compact('location', 'domains', 'selectedDomains'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $validated = $request->validated();

        $slug = Str::slug($validated['name']);
        $count = Location::withTrashed()->where('slug', $slug)->where('id', '!=', $location->id)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('locations', 'public');
        }

        $location->update($validated);
        $location->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted successfully.');
    }
}
