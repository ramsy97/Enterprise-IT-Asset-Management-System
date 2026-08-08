<?php

namespace App\Exports;

use App\Models\AuditRecord;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $filters = []) {}

    public function query(): Builder
    {
        return app(\App\Services\ReportService::class)->audits($this->filters);
    }

    public function headings(): array
    {
        return [
            'Asset ID', 'Asset Name', 'Audit Batch', 'Audit Date',
            'Auditor', 'Status', 'Condition', 'Location Match', 'Findings',
        ];
    }

    public function map($record): array
    {
        return [
            $record->asset?->asset_code,
            $record->asset?->asset_name,
            $record->audit_batch_id,
            $record->audit_date?->format('Y-m-d'),
            $record->auditor?->name,
            ucfirst(str_replace('_', ' ', $record->status->value)),
            $record->condition,
            $record->location_match ? 'Yes' : 'No',
            $record->findings,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
