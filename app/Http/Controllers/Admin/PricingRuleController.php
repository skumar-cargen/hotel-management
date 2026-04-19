<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdjustmentType;
use App\Enums\PricingRuleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePricingRuleRequest;
use App\Http\Requests\Admin\UpdatePricingRuleRequest;
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
                        PricingRuleType::DomainMarkup->value => 'primary',
                        PricingRuleType::Seasonal->value => 'info',
                        PricingRuleType::DateRange->value => 'warning',
                        PricingRuleType::Category->value => 'secondary',
                        PricingRuleType::DayOfWeek->value => 'dark',
                    ];
                    $color = $colors[$rule->type] ?? 'secondary';

                    return '<span class="badge bg-'.$color.'">'.e(str_replace('_', ' ', ucfirst($rule->type))).'</span>';
                })
                ->addColumn('domain_name', function ($rule) {
                    return e($rule->domain->name ?? '-');
                })
                ->addColumn('hotel_name', function ($rule) {
                    return e($rule->hotel->name ?? '-');
                })
                ->addColumn('adjustment', function ($rule) {
                    if ($rule->adjustment_type === AdjustmentType::Percentage->value) {
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

    public function store(StorePricingRuleRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdatePricingRuleRequest $request, PricingRule $pricingRule)
    {
        $validated = $request->validated();

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
