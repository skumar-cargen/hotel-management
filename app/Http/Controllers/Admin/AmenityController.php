<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Amenity::query();

            return DataTables::of($query)
                ->addColumn('icon_display', function ($amenity) {
                    return $amenity->icon
                        ? '<i class="bx '.e($amenity->icon).' fs-4"></i>'
                        : '-';
                })
                ->addColumn('status', function ($amenity) {
                    return $amenity->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($amenity) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.amenities.edit', $amenity).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.amenities.destroy', $amenity).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['icon_display', 'status', 'action'])
                ->make(true);
        }

        return view('admin.amenities.index');
    }

    public function create()
    {
        return view('admin.amenities.edit', ['amenity' => new Amenity]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Amenity::create($validated);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $amenity->update($validated);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity deleted successfully.');
    }
}
