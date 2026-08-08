<x-app-layout>
    <x-slot name="title">Maintenance Report</x-slot>

    <x-page-header
        title="Maintenance Report"
        subtitle="Filter and export maintenance activity.">

        <x-slot name="actions">
            <a href="{{ route('reports.maintenance.excel', $filters) }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">table_view</span>
                Excel
            </a>
            <a href="{{ route('reports.maintenance.pdf', $filters) }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Report Center
            </a>
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-outline-variant/70 p-4">
            <form method="GET" action="{{ route('reports.maintenance') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="w-44">
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
                <div class="w-44">
                    <label for="from" class="label-field">From</label>
                    <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}" class="input-field">
                </div>
                <div class="w-44">
                    <label for="to" class="label-field">To</label>
                    <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}" class="input-field">
                </div>
                <button type="submit" class="btn-primary">Apply</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Type</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Scheduled</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Completed</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Technician</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($records as $record)
                        <tr class="transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 font-mono text-mono">{{ $record->asset?->asset_code }}<span class="ml-2 font-sans text-body-sm text-on-surface-variant">{{ $record->asset?->asset_name }}</span></td>
                            <td class="px-4 py-3">{{ $record->type->label() }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $record->scheduled_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $record->completed_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $record->technician?->name ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$record->status" /></td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($record->cost) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="No records found" message="No maintenance records match the selected filters." />
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
