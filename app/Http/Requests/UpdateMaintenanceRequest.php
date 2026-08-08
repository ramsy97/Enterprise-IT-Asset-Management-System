<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('maintenance.update');
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'type' => ['required', Rule::enum(MaintenanceType::class)],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(MaintenanceStatus::class)],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'result' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
