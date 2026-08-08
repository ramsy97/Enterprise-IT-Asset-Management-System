<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('categories.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'unique:asset_categories,name'],
            'code_prefix' => ['required', 'string', 'max:10', 'alpha_num', 'unique:asset_categories,code_prefix'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
