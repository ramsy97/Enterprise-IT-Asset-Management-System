<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function store(array $data): Asset
    {
        return DB::transaction(function () use ($data) {
            $asset = Asset::create([
                ...$data,
                'asset_code' => $this->generateAssetCode($data['asset_category_id']),
            ]);

            $asset->qr_path = $this->qrCodeService->generate($asset);
            $asset->save();

            ActivityLogger::log('asset', "Registered asset {$asset->asset_code} ({$asset->asset_name})", [
                'asset_id' => $asset->id,
            ]);

            return $asset;
        });
    }

    public function update(Asset $asset, array $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            $categoryChanged = ! empty($data['asset_category_id'])
                && $data['asset_category_id'] != $asset->asset_category_id;

            if ($categoryChanged) {
                $data['asset_code'] = $this->generateAssetCode($data['asset_category_id']);
                $this->qrCodeService->flush($asset);
            }

            $asset->update($data);

            if ($categoryChanged) {
                $asset->qr_path = $this->qrCodeService->generate($asset);
                $asset->save();
            }

            ActivityLogger::log('asset', "Updated asset {$asset->asset_code}", [
                'asset_id' => $asset->id,
            ]);

            return $asset;
        });
    }

    public function delete(Asset $asset): void
    {
        DB::transaction(function () use ($asset) {
            ActivityLogger::log('asset', "Deleted asset {$asset->asset_code} ({$asset->asset_name})");
            $this->qrCodeService->flush($asset);
            $asset->delete();
        });
    }

    public function generateAssetCode(int $categoryId): string
    {
        $category = AssetCategory::findOrFail($categoryId);
        $prefix = 'IT-'.$category->code_prefix.'-';

        $last = Asset::where('asset_code', 'like', $prefix.'%')
            ->orderByDesc('asset_code')
            ->value('asset_code');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function assignToEmployee(Asset $asset, AssetAssignment $assignment): void
    {
        $asset->update([
            'status' => 'assigned',
            'current_holder_id' => $assignment->employee_id,
        ]);
    }

    public function markAvailable(Asset $asset): void
    {
        $asset->update([
            'status' => 'available',
            'current_holder_id' => null,
        ]);
    }

    public function markMaintenance(Asset $asset): void
    {
        $asset->update(['status' => 'maintenance']);
    }

    public function restoreFromMaintenance(Asset $asset): void
    {
        $asset->update([
            'status' => $asset->current_holder_id ? 'assigned' : 'available',
        ]);
    }

    public function retire(Asset $asset): void
    {
        $asset->update([
            'status' => 'retired',
            'current_holder_id' => null,
        ]);
    }
}
