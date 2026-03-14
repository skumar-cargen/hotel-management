<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Career::query()->with('domains');

            return DataTables::of($query)
                ->addColumn('domain_list', function ($career) {
                    return $career->domains->pluck('name')->take(3)->map(fn ($n) => '<span class="badge bg-secondary me-1">'.e($n).'</span>')->implode('');
                })
                ->addColumn('job_type_label', function ($career) {
                    $labels = [
                        'full_time' => ['Full Time', 'primary'],
                        'part_time' => ['Part Time', 'info'],
                        'contract' => ['Contract', 'warning'],
                        'internship' => ['Internship', 'secondary'],
                    ];
                    $label = $labels[$career->job_type] ?? ['Unknown', 'secondary'];

                    return '<span class="badge bg-'.$label[1].'">'.$label[0].'</span>';
                })
                ->addColumn('last_date', function ($career) {
                    $date = $career->last_apply_date->format('M j, Y');
                    $isOpen = $career->last_apply_date->gte(now()->startOfDay());
                    $color = $isOpen ? 'success' : 'danger';

                    return '<span class="text-'.$color.'" style="font-size:.82rem;">'.$date.'</span>';
                })
                ->addColumn('status', function ($career) {
                    return $career->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($career) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.careers.edit', $career).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.careers.destroy', $career).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this career posting?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['domain_list', 'job_type_label', 'last_date', 'status', 'action'])
                ->make(true);
        }

        return view('admin.careers.index');
    }

    public function create()
    {
        return view('admin.careers.edit', [
            'career' => new Career,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full_time,part_time,contract,internship',
            'department' => 'required|string|max:255',
            'about_role' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'what_we_offer' => 'nullable|string',
            'last_apply_date' => 'required|date',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $career = Career::create($validated);
        $career->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.careers.index')->with('success', 'Career posting created successfully.');
    }

    public function edit(Career $career)
    {
        $career->load('domains');

        return view('admin.careers.edit', compact('career'));
    }

    public function update(Request $request, Career $career)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full_time,part_time,contract,internship',
            'department' => 'required|string|max:255',
            'about_role' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'what_we_offer' => 'nullable|string',
            'last_apply_date' => 'required|date',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $career->update($validated);
        $career->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.careers.index')->with('success', 'Career posting updated successfully.');
    }

    public function destroy(Career $career)
    {
        $career->delete();

        return redirect()->route('admin.careers.index')->with('success', 'Career posting deleted successfully.');
    }
}
