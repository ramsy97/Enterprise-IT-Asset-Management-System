<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('licenses.create');
    }

    public function rules(): array
    {
        return [
            'software_name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:120'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'total_licenses' => ['required', 'integer', 'min:1'],
            'used_licenses' => ['nullable', 'integer', 'min:0', 'lte:total_licenses'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
