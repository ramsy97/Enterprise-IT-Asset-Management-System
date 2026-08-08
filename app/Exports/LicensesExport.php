<?php

namespace App\Exports;

use App\Models\SoftwareLicense;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicensesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query(): Builder
    {
        return app(\App\Services\ReportService::class)->licenses();
    }

    public function headings(): array
    {
        return [
            'Software Name', 'Vendor', 'Total Licenses', 'Used Licenses',
            'Available', 'Usage %', 'Purchase Date', 'Cost', 'Expires At',
        ];
    }

    public function map($license): array
    {
        return [
            $license->software_name,
            $license->vendor,
            $license->total_licenses,
            $license->used_licenses,
            $license->getAvailableLicenses(),
            $license->getUsagePercent(),
            $license->purchase_date?->format('Y-m-d'),
            (float) $license->purchase_cost,
            $license->expires_at?->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
