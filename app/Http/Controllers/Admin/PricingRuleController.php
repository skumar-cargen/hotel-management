<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PricingRuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PricingRule::query()->with(['domain', 'hotel'])->latest();

            return DataTables::of($query)
                ->addColumn('type_badge', function ($rule) {
                    $colors = [
                        'domain_markup' => 'primary',
                        'seasonal' => 'info',
                        'date_range' => 'warning',
                        'category' => 'secondary',
                        'day_of_week' => 'dark',
                    ];
                    $color = $colors[$rule->type] ?? 'secondary';

                    return '<span class="badge bg-'.$color.'">'.e(str_replace('_', ' ', ucfirst($rule->type))).'</span>';
                })
                ->addColumn('domain_name', function ($rule) {
                    return $rule->domain->name ?? '-';
                })
                ->addColumn('hotel_name', function ($rule) {
                    return $rule->hotel->name ?? '-';
                })
                ->addColumn('adjustment', function ($rule) {
                    if ($rule->adjustment_type === 'percentage') {
                        $sign = $rule->adjustment_value >= 0 ? '+' : '';

                        return $sign.$rule->adjustment_value.'%';
                    }
                    $sign = $rule->adjustment_value >= 0 ? '+' : '';

                    return $sign.number_format($rule->adjustment_value, 2).' AED';
                })
                ->addColumn('status', function ($rule) {
                    return $rule->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($rule) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.pricing-rules.edit', $rule).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.pricing-rules.destroy', $rule).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['type_badge', 'adjustment', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pricing-rules.index');
    }

    public function create()
    {
        return view('admin.pricing-rules.edit', [
            'pricingRule' => new PricingRule,
            'domains' => Domain::active()->get(),
            'hotels' => Hotel::active()->get(),
            'locations' => Location::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:domain_markup,seasonal,date_range,category,day_of_week',
            'domain_id' => 'nullable|exists:domains,id',
            'hotel_id' => 'nullable|exists:hotels,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'location_id' => 'nullable|exists:locations,id',
            'adjustment_type' => 'required|in:percentage,fixed_amount',
            'adjustment_value' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'days_of_week' => 'nullable|array',
            'priority' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        PricingRule::create($validated);

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule created successfully.');
    }

    public function edit(PricingRule $pricingRule)
    {
        $domains = Domain::active()->get();
        $hotels = Hotel::active()->get();
        $locations = Location::active()->get();

        return view('admin.pricing-rules.edit', compact('pricingRule', 'domains', 'hotels', 'locations'));
    }

    public function update(Request $request, PricingRule $pricingRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:domain_markup,seasonal,date_range,category,day_of_week',
            'domain_id' => 'nullable|exists:domains,id',
            'hotel_id' => 'nullable|exists:hotels,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'location_id' => 'nullable|exists:locations,id',
            'adjustment_type' => 'required|in:percentage,fixed_amount',
            'adjustment_value' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'days_of_week' => 'nullable|array',
            'priority' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $nullableFields = ['domain_id', 'hotel_id', 'room_type_id', 'location_id', 'start_date', 'end_date', 'days_of_week'];
        foreach ($nullableFields as $field) {
            if (!array_key_exists($field, $validated)) {
                $validated[$field] = null;
            }
        }

        $pricingRule->update($validated);

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule updated successfully.');
    }

    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule deleted successfully.');
    }
}
