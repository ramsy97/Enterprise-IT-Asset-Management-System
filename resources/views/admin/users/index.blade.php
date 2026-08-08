<x-app-layout>
    <x-slot name="title">Users & Roles</x-slot>

    <x-page-header
        title="Users"
        subtitle="Manage user accounts, roles, and activation status.">

        <x-slot name="actions">
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add User
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">shield</span>
                Roles & Permissions
            </a>
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">User</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Department</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Role</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Assets</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($users as $user)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary/10 text-xs font-bold text-secondary">{{ $user->initials() }}</span>
                                    <div>
                                        <p class="font-medium text-on-surface">{{ $user->name }}</p>
                                        <p class="font-mono text-mono text-on-surface-variant">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $user->department ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @foreach ($user->roles as $role)
                                    <span class="badge badge-blue">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-right text-on-surface-variant">{{ $user->assigned_assets_count }}</td>
                            <td class="px-4 py-3">
                                @if ($user->is_active)
                                    <span class="badge badge-green">Active</span>
                                @else
                                    <span class="badge badge-red">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <a href="{{ route('admin.users.edit', $user) }}" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                            @csrf
                                            <button type="submit" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                                <span class="material-symbols-outlined text-[18px]">{{ $user->is_active ? 'person_off' : 'person' }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="No users found" message="Add a user to get started." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
