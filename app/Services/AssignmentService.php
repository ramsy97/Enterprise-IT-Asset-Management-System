<?php

namespace App\Services;

use App\Enums\AssetStatus;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public function __construct(
        private readonly AssetService $assetService,
    ) {}

    public function create(AssetAssignment $assignment, int $approvedById = null): AssetAssignment
    {
        return DB::transaction(function () use ($assignment, $approvedById) {
            $assignment->approved_by = $approvedById;
            $assignment->approved_at = $approvedById ? now()->toDateString() : null;
            $assignment->assigned_date = $approvedById ? now()->toDateString() : null;
            $assignment->status = $approvedById ? 'approved' : 'pending';
            $assignment->save();

            if ($approvedById) {
                $this->assetService->assignToEmployee($assignment->asset, $assignment);
            }

            ActivityLogger::log('assignment', "Assignment request created for {$assignment->asset->asset_code} → {$assignment->employee->name}");

            return $assignment;
        });
    }

    public function approve(AssetAssignment $assignment, User $approver): AssetAssignment
    {
        return DB::transaction(function () use ($assignment, $approver) {
            if ($assignment->asset->status === AssetStatus::Assigned) {
                throw new \RuntimeException('This asset is already assigned to another employee.');
            }

            $assignment->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now()->toDateString(),
                'assigned_date' => now()->toDateString(),
            ]);

            $this->assetService->assignToEmployee($assignment->asset, $assignment);

            ActivityLogger::log('assignment', "Assignment approved: {$assignment->asset->asset_code} → {$assignment->employee->name}", [
                'assignment_id' => $assignment->id,
            ]);

            return $assignment;
        });
    }

    public function reject(AssetAssignment $assignment, User $approver): AssetAssignment
    {
        $assignment->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'rejected_at' => now()->toDateString(),
        ]);

        ActivityLogger::log('assignment', "Assignment rejected: {$assignment->asset->asset_code} → {$assignment->employee->name}");

        return $assignment;
    }

    public function returnAsset(AssetAssignment $assignment): AssetAssignment
    {
        return DB::transaction(function () use ($assignment) {
            $assignment->update([
                'status' => 'returned',
                'return_date' => now()->toDateString(),
            ]);

            $this->assetService->markAvailable($assignment->asset);

            ActivityLogger::log('assignment', "Asset returned: {$assignment->asset->asset_code} from {$assignment->employee->name}");

            return $assignment;
        });
    }
}
