<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'position',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'employee_id');
    }

    public function assignedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_holder_id');
    }

    public function maintainedRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'technician_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AuditRecord::class, 'audited_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));

        return strtoupper(collect($parts)->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join(''));
    }

    public function roleName(): string
    {
        return $this->roles->first()?->name ?? 'N/A';
    }

    public function homeRoute(): string
    {
        return match ($this->roleName()) {
            'ADMIN' => 'admin.dashboard',
            'IT STAFF' => 'staff.dashboard',
            'MANAGER' => 'manager.dashboard',
            default => 'dashboard',
        };
    }
}
