<?php

namespace App\Policies;

use App\Models\SoftwareLicense;
use App\Models\User;

class SoftwareLicensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('licenses.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('licenses.create');
    }

    public function update(User $user, SoftwareLicense $license): bool
    {
        return $user->hasPermissionTo('licenses.update');
    }

    public function delete(User $user, SoftwareLicense $license): bool
    {
        return $user->hasPermissionTo('licenses.delete');
    }
}
