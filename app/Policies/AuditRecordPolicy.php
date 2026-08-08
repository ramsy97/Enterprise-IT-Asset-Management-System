<?php

namespace App\Policies;

use App\Models\AuditRecord;
use App\Models\User;

class AuditRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('audits.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('audits.create');
    }

    public function update(User $user, AuditRecord $audit): bool
    {
        return $user->hasPermissionTo('audits.update');
    }

    public function delete(User $user, AuditRecord $audit): bool
    {
        return $user->hasPermissionTo('audits.delete');
    }
}
