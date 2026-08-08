<?php

namespace App\Policies;

use App\Models\MaintenanceRecord;
use App\Models\User;

class MaintenanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('maintenance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('maintenance.create');
    }

    public function update(User $user, MaintenanceRecord $record): bool
    {
        return $user->hasPermissionTo('maintenance.update');
    }

    public function delete(User $user, MaintenanceRecord $record): bool
    {
        return $user->hasPermissionTo('maintenance.delete');
    }
}
