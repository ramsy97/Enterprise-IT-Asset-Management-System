<x-app-layout>
    <x-slot name="title">Audit Management</x-slot>

    <x-page-header
        title="Audit Management"
        subtitle="Physical and logical audits of the asset inventory.">

        <x-slot name="actions">
            @can('audits.create')
                <a href="{{ route('audits.create') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    New Audit
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-outline-variant/70 p-4">
            <form method="GET" action="{{ route('audits.index') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="w-48">
                    <label for="status" class="label-field">Status</label>
                    <select id="status" name="status" class="input-field">
                        <option value="">All</option>
                        @foreach (\App\Enums\AuditStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-56">
                    <label for="asset_id" class="label-field">Asset</label>
                    <select id="asset_id" name="asset_id" class="input-field">
                        <option value="">All assets</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(($filters['asset_id'] ?? null) == $asset->id)>{{ $asset->asset_code }} — {{ $asset->asset_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Apply</button>
                <a href="{{ route('audits.index') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                    Clear
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Batch</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Audit Date</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Auditor</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Condition</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($audits as $audit)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3">
                                <a href="{{ route('audits.show', $audit) }}" class="font-mono text-mono hover:text-secondary">{{ $audit->asset?->asset_code }}</a>
                                <p class="text-body-sm text-on-surface-variant">{{ $audit->asset?->asset_name }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-mono text-on-surface-variant">{{ $audit->audit_batch_id }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $audit->audit_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $audit->auditor?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $audit->condition ?: '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$audit->status" /></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <a href="{{ route('audits.show', $audit) }}" title="View" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    @can('audits.update')
                                        <a href="{{ route('audits.edit', $audit) }}" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state
                                    title="No audit records"
                                    message="No audit records match your filters." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($audits->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $audits->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
