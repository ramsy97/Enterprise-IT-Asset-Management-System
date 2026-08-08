<x-app-layout>
    <x-slot name="title">Audit Report</x-slot>

    <x-page-header
        title="Audit Report"
        subtitle="Filter and export audit results.">

        <x-slot name="actions">
            <a href="{{ route('reports.audits.excel', $filters) }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">table_view</span>
                Excel
            </a>
            <a href="{{ route('reports.audits.pdf', $filters) }}" class="btn-secondary">
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
            <form method="GET" action="{{ route('reports.audits') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="w-44">
                    <label for="status" class="label-field">Status</label>
                    <select id="status" name="status" class="input-field">
                        <option value="">All</option>
                        @foreach (\App\Enums\AuditStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
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
                        <th class="px-4 py-3 text-label-md text-on-surface">Batch</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Audit Date</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Auditor</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Condition</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Location</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($audits as $audit)
                        <tr class="transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 font-mono text-mono">{{ $audit->asset?->asset_code }}<span class="ml-2 font-sans text-body-sm text-on-surface-variant">{{ $audit->asset?->asset_name }}</span></td>
                            <td class="px-4 py-3 font-mono text-mono text-on-surface-variant">{{ $audit->audit_batch_id }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $audit->audit_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $audit->auditor?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $audit->condition ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($audit->location_match)
                                    <span class="badge badge-green">Matched</span>
                                @else
                                    <span class="badge badge-red">Mismatch</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-status-badge :value="$audit->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="No records found" message="No audit records match the selected filters." />
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
