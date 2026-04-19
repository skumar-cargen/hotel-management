<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains',
            'default_language' => 'required|string|max:5',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg,jpg,jpeg,webp|max:512',
            'about_us' => 'nullable|string',
            'about_us_meta_title' => 'nullable|string|max:255',
            'about_us_meta_description' => 'nullable|string|max:500',
            'about_us_canonical_url' => 'nullable|url|max:255',
            'privacy_policy' => 'nullable|string',
            'privacy_policy_meta_title' => 'nullable|string|max:255',
            'privacy_policy_meta_description' => 'nullable|string|max:500',
            'privacy_policy_canonical_url' => 'nullable|url|max:255',
            'terms_conditions' => 'nullable|string',
            'terms_conditions_meta_title' => 'nullable|string|max:255',
            'terms_conditions_meta_description' => 'nullable|string|max:500',
            'terms_conditions_canonical_url' => 'nullable|url|max:255',
        ];
    }
}
