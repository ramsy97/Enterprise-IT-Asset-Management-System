<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('users.manage');

        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->withCount('users')->orderBy('name')->get(),
            'permissions' => $this->permissionGroups(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('users.manage');

        $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role = Role::create(['name' => strtoupper($request->name)]);

        if (! empty($request->permissions)) {
            $role->syncPermissions($request->permissions);
        }

        ActivityLogger::log('role', "Role created: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', "Role {$role->name} created.");
    }

    public function create(): View
    {
        $this->authorize('users.manage');

        return view('admin.roles.create', ['permissions' => $this->permissionGroups()]);
    }

    public function edit(Role $role): View
    {
        $this->authorize('users.manage');

        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->permissionGroups(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('users.manage');

        $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        if ($role->name === 'ADMIN') {
            $request->merge(['permissions' => \Spatie\Permission\Models\Permission::pluck('name')->toArray()]);
        }

        $role->update(['name' => strtoupper($request->name)]);
        $role->syncPermissions($request->permissions ?? []);

        ActivityLogger::log('role', "Role updated: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', "Role {$role->name} updated.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('users.manage');

        if (in_array($role->name, ['ADMIN', 'IT STAFF', 'MANAGER'], true)) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if (User::role($role->name)->exists()) {
            return back()->with('error', 'Role is assigned to users and cannot be deleted.');
        }

        ActivityLogger::log('role', "Role deleted: {$role->name}");
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    private function permissionGroups(): array
    {
        $groups = DB::table('permissions')->orderBy('name')->get(['name', 'id'])->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return $groups->map(function ($perms) {
            return $perms->mapWithKeys(fn ($p) => [$p->name => $p->name]);
        })->all();
    }
}
