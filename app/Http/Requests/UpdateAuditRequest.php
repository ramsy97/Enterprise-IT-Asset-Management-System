<?php

namespace App\Http\Requests;

use App\Enums\AuditStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('audits.update');
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'audit_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(AuditStatus::class)],
            'condition' => ['nullable', 'string', 'max:100'],
            'location_match' => ['sometimes', 'boolean'],
            'findings' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }
}
