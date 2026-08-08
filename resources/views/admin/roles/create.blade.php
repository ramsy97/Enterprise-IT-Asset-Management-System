<x-app-layout>
    <x-slot name="title">New Role</x-slot>

    <x-page-header
        title="New Role"
        subtitle="Create a role and select its permissions." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.roles.store') }}" class="max-w-5xl" x-data="{ open: false }">
        @csrf

        <x-card title="Role Details">
            <label for="name" class="label-field">Role Name <span class="text-error">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. SUPPORT LEAD" class="input-field max-w-md">
        </x-card>

        <x-card title="Permissions">
            <div class="mb-4 flex items-center gap-3">
                <label class="inline-flex cursor-pointer items-center gap-2">
                    <input type="checkbox" x-model="open" @change="$el.closest('form').querySelectorAll('input[name^=permissions]').forEach(c => c.checked = open)" class="h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary">
                    <span class="text-label-lg text-on-surface">Select all</span>
                </label>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($permissions as $group => $perms)
                    <div>
                        <p class="mb-2 text-label-lg font-semibold uppercase tracking-wide text-on-surface-variant">{{ str_replace('_', ' ', $group) }}</p>
                        <div class="space-y-1.5">
                            @foreach ($perms as $permissionName)
                                <label class="flex cursor-pointer items-start gap-2 rounded p-1.5 transition-colors hover:bg-[#F8FAFC]">
                                    <input type="checkbox" name="permissions[]" value="{{ $permissionName }}" @checked(in_array($permissionName, old('permissions', []), true)) class="mt-0.5 h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary">
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
                <span class="material-symbols-outlined text-[18px]">shield</span>
                Create Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
