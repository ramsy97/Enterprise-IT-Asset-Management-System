<?php

namespace App\Policies;

use App\Models\AssetLocation;
use App\Models\User;

class AssetLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('locations.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('locations.manage');
    }

    public function update(User $user, AssetLocation $location): bool
    {
        return $user->hasPermissionTo('locations.manage');
    }

    public function delete(User $user, AssetLocation $location): bool
    {
        return $user->hasPermissionTo('locations.manage');
    }
}
