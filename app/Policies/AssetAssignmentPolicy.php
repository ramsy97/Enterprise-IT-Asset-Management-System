<?php

namespace App\Policies;

use App\Models\AssetAssignment;
use App\Models\User;

class AssetAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('assignments.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('assignments.request');
    }

    public function approve(User $user, AssetAssignment $assignment): bool
    {
        return $user->hasPermissionTo('assignments.approve');
    }

    public function reject(User $user, AssetAssignment $assignment): bool
    {
        return $user->hasPermissionTo('assignments.approve');
    }

    public function returnAsset(User $user, AssetAssignment $assignment): bool
    {
        return $user->hasPermissionTo('assignments.return');
    }
}
