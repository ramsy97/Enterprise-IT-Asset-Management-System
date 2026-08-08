<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('categories.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('asset_categories', 'name')->ignore($this->category)],
            'code_prefix' => ['required', 'string', 'max:10', 'alpha_num', Rule::unique('asset_categories', 'code_prefix')->ignore($this->category)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
