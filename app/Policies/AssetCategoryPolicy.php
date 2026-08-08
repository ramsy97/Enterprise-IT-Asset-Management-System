<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('categories.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('categories.manage');
    }

    public function update(User $user, AssetCategory $category): bool
    {
        return $user->hasPermissionTo('categories.manage');
    }

    public function delete(User $user, AssetCategory $category): bool
    {
        return $user->hasPermissionTo('categories.manage');
    }
}
