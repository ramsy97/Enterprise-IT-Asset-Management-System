<?php

namespace App\Exports;

use App\Models\MaintenanceRecord;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenancesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $filters = []) {}

    public function query(): Builder
    {
        return app(\App\Services\ReportService::class)->maintenance($this->filters);
    }

    public function headings(): array
    {
        return [
            'Asset ID', 'Asset Name', 'Type', 'Technician',
            'Scheduled Date', 'Completed Date', 'Status', 'Cost', 'Description',
        ];
    }

    public function map($record): array
    {
        return [
            $record->asset?->asset_code,
            $record->asset?->asset_name,
            ucfirst(str_replace('_', ' ', $record->type->value)),
            $record->technician?->name,
            $record->scheduled_date?->format('Y-m-d'),
            $record->completed_date?->format('Y-m-d'),
            ucfirst(str_replace('_', ' ', $record->status->value)),
            (float) $record->cost,
            $record->description,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
