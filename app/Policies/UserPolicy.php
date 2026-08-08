<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.manage');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasPermissionTo('users.manage');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasPermissionTo('users.manage') && $user->id !== $target->id;
    }
}
