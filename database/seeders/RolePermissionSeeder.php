<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'dashboard.view',
        // assets
        'assets.view',
        'assets.create',
        'assets.update',
        'assets.delete',
        // master data
        'categories.manage',
        'locations.manage',
        // assignments
        'assignments.view',
        'assignments.request',
        'assignments.approve',
        'assignments.return',
        // maintenance
        'maintenance.view',
        'maintenance.create',
        'maintenance.update',
        'maintenance.delete',
        // warranty
        'warranty.view',
        // licenses
        'licenses.view',
        'licenses.create',
        'licenses.update',
        'licenses.delete',
        // audits
        'audits.view',
        'audits.create',
        'audits.update',
        'audits.delete',
        // reports
        'reports.view',
        // user management
        'users.manage',
        'roles.manage',
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }

        $admin = Role::updateOrCreate(['name' => 'ADMIN'], ['guard_name' => 'web']);
        $admin->syncPermissions(self::PERMISSIONS);

        $staff = Role::updateOrCreate(['name' => 'IT STAFF'], ['guard_name' => 'web']);
        $staff->syncPermissions([
            'dashboard.view',
            'assets.view',
            'assets.create',
            'assets.update',
            'categories.manage',
            'locations.manage',
            'assignments.view',
            'assignments.request',
            'assignments.return',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',
            'warranty.view',
            'licenses.view',
            'licenses.create',
            'licenses.update',
            'audits.view',
            'audits.create',
            'audits.update',
        ]);

        $manager = Role::updateOrCreate(['name' => 'MANAGER'], ['guard_name' => 'web']);
        $manager->syncPermissions([
            'dashboard.view',
            'assets.view',
            'assignments.view',
            'assignments.approve',
            'maintenance.view',
            'warranty.view',
            'licenses.view',
            'audits.view',
            'reports.view',
        ]);
    }
}
