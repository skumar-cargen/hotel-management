<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'num_rooms' => ['required', 'integer', 'min:1', 'max:10'],
            'num_adults' => ['required', 'integer', 'min:1'],
            'num_children' => ['sometimes', 'integer', 'min:0'],
            'guest_first_name' => ['required', 'string', 'max:100'],
            'guest_last_name' => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'guest_nationality' => ['sometimes', 'string', 'max:100'],
            'special_requests' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
