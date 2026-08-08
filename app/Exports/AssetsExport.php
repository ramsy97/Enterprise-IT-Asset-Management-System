<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $filters = []) {}

    public function query(): Builder
    {
        return app(\App\Services\ReportService::class)->assets($this->filters);
    }

    public function headings(): array
    {
        return [
            'Asset ID', 'Asset Name', 'Category', 'Brand', 'Model',
            'Serial Number', 'Purchase Date', 'Purchase Price', 'Location',
            'Status', 'Warranty Expiry', 'Current Holder',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->asset_name,
            $asset->category?->name,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->purchase_date?->format('Y-m-d'),
            (float) $asset->purchase_price,
            $asset->location?->name,
            ucfirst(str_replace('_', ' ', $asset->status->value)),
            $asset->warranty_expires_at?->format('Y-m-d'),
            $asset->currentHolder?->name,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
