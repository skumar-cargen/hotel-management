<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DealController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Deal::query()->with('domains')->latest();

            return DataTables::of($query)
                ->addColumn('discount', function ($deal) {
                    if ($deal->discount_type === 'percentage') {
                        return '<span class="badge bg-primary">'.$deal->discount_value.'%</span>';
                    }

                    return '<span class="badge bg-info">AED '.number_format($deal->discount_value, 2).'</span>';
                })
                ->addColumn('domain_list', function ($deal) {
                    return $deal->domains->pluck('name')->take(3)->map(fn ($n) => '<span class="badge bg-secondary me-1">'.e($n).'</span>')->implode('');
                })
                ->addColumn('validity', function ($deal) {
                    $start = $deal->start_date->format('M j, Y');
                    $end = $deal->end_date->format('M j, Y');
                    $now = now()->toDateString();
                    $isCurrent = $deal->start_date->toDateString() <= $now && $deal->end_date->toDateString() >= $now;
                    $color = $isCurrent ? 'success' : 'secondary';

                    return '<span class="text-'.$color.'" style="font-size:.82rem;">'.$start.' — '.$end.'</span>';
                })
                ->addColumn('status', function ($deal) {
                    return $deal->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($deal) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.deals.edit', $deal).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.deals.destroy', $deal).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this deal?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['discount', 'domain_list', 'validity', 'status', 'action'])
                ->make(true);
        }

        return view('admin.deals.index');
    }

    public function create()
    {
        return view('admin.deals.edit', [
            'deal' => new Deal,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hotels' => 'nullable|array',
            'hotels.*' => 'exists:hotels,id',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $deal = Deal::create($validated);
        $deal->hotels()->sync($request->input('hotels', []));
        $deal->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.deals.index')->with('success', 'Deal created successfully.');
    }

    public function edit(Deal $deal)
    {
        $deal->load(['hotels', 'domains']);

        return view('admin.deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hotels' => 'nullable|array',
            'hotels.*' => 'exists:hotels,id',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $deal->update($validated);
        $deal->hotels()->sync($request->input('hotels', []));
        $deal->domains()->sync($request->input('domains', []));

        return redirect()->route('admin.deals.index')->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()->route('admin.deals.index')->with('success', 'Deal deleted successfully.');
    }
}
