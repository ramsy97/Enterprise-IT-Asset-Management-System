<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('locations.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', 'unique:asset_locations,name'],
            'building' => ['nullable', 'string', 'max:150'],
            'floor' => ['nullable', 'string', 'max:50'],
            'room' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
        ];
    }
}
