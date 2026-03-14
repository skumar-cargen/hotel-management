<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CareerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'cover_letter' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }
}
