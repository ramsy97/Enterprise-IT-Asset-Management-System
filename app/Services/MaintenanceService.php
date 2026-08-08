<?php

namespace App\Services;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRecord;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    public function __construct(
        private readonly AssetService $assetService,
    ) {}

    public function create(MaintenanceRecord $record): MaintenanceRecord
    {
        return DB::transaction(function () use ($record) {
            $record->save();

            if ($record->status === MaintenanceStatus::InProgress) {
                $this->assetService->markMaintenance($record->asset);
            }

            ActivityLogger::log('maintenance', "Maintenance scheduled for {$record->asset->asset_code} on {$record->scheduled_date}", [
                'maintenance_id' => $record->id,
            ]);

            return $record;
        });
    }

    public function update(MaintenanceRecord $record, array $data): MaintenanceRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $record->update($data);

            if (in_array($record->status, [MaintenanceStatus::Completed, MaintenanceStatus::Cancelled])) {
                $this->assetService->restoreFromMaintenance($record->asset);
            } elseif ($record->status === MaintenanceStatus::InProgress) {
                $this->assetService->markMaintenance($record->asset);
            }

            ActivityLogger::log('maintenance', "Maintenance updated for {$record->asset->asset_code}");

            return $record;
        });
    }

    public function complete(MaintenanceRecord $record, array $data): MaintenanceRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $record->update([
                'status' => 'completed',
                'completed_date' => $data['completed_date'] ?? now()->toDateString(),
                'cost' => $data['cost'] ?? $record->cost,
                'result' => $data['result'] ?? $record->result,
            ]);

            $this->assetService->restoreFromMaintenance($record->asset);

            ActivityLogger::log('maintenance', "Maintenance completed for {$record->asset->asset_code}", [
                'cost' => $record->cost,
            ]);

            return $record;
        });
    }

    public function cancel(MaintenanceRecord $record): MaintenanceRecord
    {
        return DB::transaction(function () use ($record) {
            $record->update(['status' => 'cancelled']);
            $this->assetService->restoreFromMaintenance($record->asset);

            ActivityLogger::log('maintenance', "Maintenance cancelled for {$record->asset->asset_code}");

            return $record;
        });
    }

    public function delete(MaintenanceRecord $record): void
    {
        DB::transaction(function () use ($record) {
            if ($record->status === MaintenanceStatus::InProgress) {
                $this->assetService->restoreFromMaintenance($record->asset);
            }
            ActivityLogger::log('maintenance', "Maintenance record deleted for {$record->asset->asset_code}");
            $record->delete();
        });
    }
}
