<x-app-layout>
    <x-slot name="title">Edit Role</x-slot>

    <x-page-header
        title="Edit Role"
        subtitle="Update the role name and its permissions." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="max-w-5xl" x-data="{ open: false }">
        @csrf
        @method('PUT')

        <x-card title="Role Details">
            <label for="name" class="label-field">Role Name <span class="text-error">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required class="input-field max-w-md">
            @if (in_array($role->name, ['ADMIN', 'IT STAFF', 'MANAGER'], true))
                <p class="mt-2 text-body-sm text-on-surface-variant">System roles keep their permission set — only the display name is editable.</p>
            @endif
        </x-card>

        <x-card title="Permissions">
            @if (! in_array($role->name, ['ADMIN', 'IT STAFF', 'MANAGER'], true))
                <div class="mb-4 flex items-center gap-3">
                    <label class="inline-flex cursor-pointer items-center gap-2">
                        <input type="checkbox" x-model="open" @change="$el.closest('form').querySelectorAll('input[name^=permissions]').forEach(c => c.checked = open)" class="h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary">
                        <span class="text-label-lg text-on-surface">Select all</span>
                    </label>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($permissions as $group => $perms)
                    <div>
                        <p class="mb-2 text-label-lg font-semibold uppercase tracking-wide text-on-surface-variant">{{ str_replace('_', ' ', $group) }}</p>
                        <div class="space-y-1.5">
                            @foreach ($perms as $permissionName)
                                <label class="flex cursor-pointer items-start gap-2 rounded p-1.5 transition-colors hover:bg-[#F8FAFC]">
                                    <input type="checkbox" name="permissions[]" value="{{ $permissionName }}"
                                        @disabled(in_array($role->name, ['ADMIN', 'IT STAFF', 'MANAGER'], true))
                                        @checked($role->hasPermissionTo($permissionName))
                                        class="mt-0.5 h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary">
                                    <span class="font-mono text-mono text-on-surface">{{ $permissionName }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Save Changes
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
