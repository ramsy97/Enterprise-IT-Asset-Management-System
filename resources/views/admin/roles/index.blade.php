<x-app-layout>
    <x-slot name="title">Roles & Permissions</x-slot>

    <x-page-header
        title="Roles & Permissions"
        subtitle="Define roles and control which permissions each role has.">

        <x-slot name="actions">
            <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span>
                New Role
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Users
            </a>
        </x-slot>
    </x-page-header>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-success/30 bg-success/10 p-4 text-body-sm text-on-success-container">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4 text-body-sm text-on-error-container">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($roles as $role)
            <x-card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-title-md font-semibold text-on-surface">{{ $role->name }}</h3>
                        <p class="mt-1 text-body-sm text-on-surface-variant">
                            <span class="font-mono text-mono">{{ $role->users_count }}</span> users ·
                            <span class="font-mono text-mono">{{ $role->permissions->count() }}</span> permissions
                        </p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 text-secondary">
                        <span class="material-symbols-outlined">shield_person</span>
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach ($role->permissions->take(6) as $permission)
                        <span class="badge badge-blue">{{ $permission->name }}</span>
                    @endforeach
                    @if ($role->permissions->count() > 6)
                        <span class="badge">+{{ $role->permissions->count() - 6 }} more</span>
                    @elseif ($role->permissions->isEmpty())
                        <span class="badge">No permissions</span>
                    @endif
                </div>

                <div class="mt-5 flex items-center gap-2 border-t border-outline-variant/40 pt-4">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn-secondary">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit
                    </a>
                    @if (! in_array($role->name, ['ADMIN', 'IT STAFF', 'MANAGER'], true) && $role->users_count === 0)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-empty-state title="No roles found" message="Create a role to start assigning permissions." />
            </div>
        @endforelse
    </div>
</x-app-layout>
