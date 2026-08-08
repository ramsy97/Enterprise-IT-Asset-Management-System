<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AuditRecord;
use App\Models\MaintenanceRecord;
use App\Models\SoftwareLicense;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function assets(array $filters = []): Builder
    {
        return Asset::with(['category', 'location', 'currentHolder'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('asset_category_id', $v))
            ->when($filters['location_id'] ?? null, fn ($q, $v) => $q->where('asset_location_id', $v))
            ->when($filters['search'] ?? null, function ($q, $v) {
                return $q->where(function ($q) use ($v) {
                    return $q->where('asset_code', 'like', "%{$v}%")
                        ->orWhere('asset_name', 'like', "%{$v}%")
                        ->orWhereHas('currentHolder', fn ($q) => $q->where('name', 'like', "%{$v}%"));
                });
            })
            ->orderBy('asset_code');
    }

    public function maintenance(array $filters = []): Builder
    {
        return MaintenanceRecord::with(['asset', 'technician'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->where('scheduled_date', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->where('scheduled_date', '<=', $v))
            ->when($filters['asset_id'] ?? null, fn ($q, $v) => $q->where('asset_id', $v))
            ->orderByDesc('scheduled_date');
    }

    public function audits(array $filters = []): Builder
    {
        return AuditRecord::with(['asset', 'auditor'])
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->where('audit_date', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->where('audit_date', '<=', $v))
            ->orderByDesc('audit_date');
    }

    public function licenses(): Builder
    {
        return SoftwareLicense::orderBy('software_name');
    }
}
