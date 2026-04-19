<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePricingRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}
