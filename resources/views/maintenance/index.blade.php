<x-app-layout>
    <x-slot name="title">Maintenance</x-slot>

    <x-page-header
        title="Maintenance Management"
        subtitle="Schedule and track preventive and corrective maintenance.">

        <x-slot name="actions">
            <a href="{{ route('maintenance.calendar') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                Calendar
            </a>
            @can('maintenance.create')
                <a href="{{ route('maintenance.create') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Schedule Maintenance
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-outline-variant/70 p-4">
            <form method="GET" action="{{ route('maintenance.index') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="w-48">
                    <label for="status" class="label-field">Status</label>
                    <select id="status" name="status" class="input-field">
                        <option value="">All</option>
                        @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-56">
                    <label for="type" class="label-field">Type</label>
                    <select id="type" name="type" class="input-field">
                        <option value="">All types</option>
                        @foreach (\App\Enums\MaintenanceType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(($filters['type'] ?? null) === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <label for="month" class="label-field">Month</label>
                    <input type="month" id="month" name="month" value="{{ $filters['month'] ?? now()->format('Y-m') }}" class="input-field">
                </div>
                <button type="submit" class="btn-primary">Apply</button>
                <a href="{{ route('maintenance.index') }}" class="btn-secondary">
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
                        <th class="px-4 py-3 text-label-md text-on-surface">Type</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Scheduled</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Technician</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Cost</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($records as $record)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3">
                                <a href="{{ route('maintenance.show', $record) }}" class="font-mono text-mono hover:text-secondary">{{ $record->asset?->asset_code }}</a>
                                <p class="text-body-sm text-on-surface-variant">{{ $record->asset?->asset_name }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $record->type->label() }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $record->scheduled_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $record->technician?->name ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$record->status" /></td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($record->cost) }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <a href="{{ route('maintenance.show', $record) }}" title="View" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    @can('maintenance.update')
                                        <a href="{{ route('maintenance.edit', $record) }}" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                        @if (in_array($record->status->value, ['scheduled', 'in_progress']))
                                            <form method="POST" action="{{ route('maintenance.complete', $record) }}" onsubmit="return confirm('Mark this maintenance as completed?')">
                                                @csrf
                                                <button type="submit" title="Mark completed" class="rounded p-1 text-[#067a4f] hover:bg-[#10b981]/10">
                                                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state
                                    title="No maintenance records"
                                    message="No maintenance records match your filters." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $records->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
