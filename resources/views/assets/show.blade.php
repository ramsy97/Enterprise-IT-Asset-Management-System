<x-app-layout>
    <x-slot name="title">{{ $asset->asset_code }}</x-slot>

    <x-page-header
        :title="$asset->asset_name"
        :subtitle="$asset->asset_code . ' · ' . ($asset->category?->name ?? 'N/A') . ' · ' . ($asset->location?->name ?? 'N/A')">

        <x-slot name="actions">
            <a href="{{ route('qr.show', $asset) }}" target="_blank" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                QR Code
            </a>
            @can('assets.update')
                <a href="{{ route('assets.edit', $asset) }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Asset
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-1">
            <x-card title="Asset Details">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Status</dt>
                        <dd class="mt-1"><x-status-badge :value="$asset->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Brand / Model</dt>
                        <dd class="mt-1 text-body-md font-medium text-on-surface">{{ $asset->brand ?: '—' }} {{ $asset->model ?: '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Serial Number</dt>
                        <dd class="mt-1 font-mono text-body-md text-on-surface">{{ $asset->serial_number ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Purchase Date</dt>
                        <dd class="mt-1 text-body-md text-on-surface">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Purchase Price</dt>
                        <dd class="mt-1 font-mono text-body-md text-on-surface">{{ rupiah($asset->purchase_price) }}</dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Warranty Expiry</dt>
                        <dd class="mt-1 text-body-md text-on-surface">
                            @if ($asset->warranty_expires_at)
                                {{ $asset->warranty_expires_at->format('d M Y') }}
                                @if ($asset->warrantyDaysLeft() < 0)
                                    <span class="badge badge-red">Expired</span>
                                @elseif ($asset->warrantyDaysLeft() <= 30)
                                    <span class="badge badge-amber">{{ $asset->warrantyDaysLeft() }} days left</span>
                                @else
                                    <span class="badge badge-green">{{ $asset->warrantyDaysLeft() }} days left</span>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Current Holder</dt>
                        <dd class="mt-1 text-body-md text-on-surface">
                            @if ($asset->currentHolder)
                                <span class="inline-flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-[10px] font-bold text-secondary">{{ $asset->currentHolder->initials() }}</span>
                                    {{ $asset->currentHolder->name }}
                                </span>
                            @else
                                <span class="italic text-on-surface-variant">Unassigned</span>
                            @endif
                        </dd>
                    </div>
                    @if ($asset->notes)
                        <div>
                            <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Notes</dt>
                            <dd class="mt-1 text-body-md text-on-surface">{{ $asset->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            <x-card title="QR Code">
                <div class="flex flex-col items-center">
                    <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
                        <img src="{{ route('qr.image', $asset) }}" alt="QR code for {{ $asset->asset_code }}" class="h-40 w-40">
                    </div>
                    <p class="mt-3 font-mono text-body-sm text-on-surface-variant">{{ $asset->asset_code }}</p>
                    <a href="{{ route('qr.show', $asset) }}" target="_blank" class="mt-2 text-body-sm font-semibold text-secondary hover:underline">View verification page</a>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-card title="Assignment History" padding="false">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Employee</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Requested</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Status</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Approved By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm">
                        @forelse ($asset->assignments as $assignment)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $assignment->employee?->name }}</td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $assignment->request_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3"><x-status-badge :value="$assignment->status" /></td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $assignment->approver?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-on-surface-variant">No assignment history.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>

            <x-card title="Maintenance History" padding="false">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Type</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Scheduled</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Technician</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Status</th>
                            <th class="px-4 py-3 text-right text-label-md text-on-surface-variant">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm">
                        @forelse ($asset->maintenanceRecords as $record)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $record->type->label() }}</td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $record->scheduled_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $record->technician?->name ?? '—' }}</td>
                                <td class="px-4 py-3"><x-status-badge :value="$record->status" /></td>
                                <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($record->cost) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-on-surface-variant">No maintenance history.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>

            <x-card title="Audit History" padding="false">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Audit Date</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Auditor</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Condition</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Status</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Findings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm">
                        @forelse ($asset->audits as $audit)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3 text-on-surface-variant">{{ $audit->audit_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $audit->auditor?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $audit->condition }}</td>
                                <td class="px-4 py-3"><x-status-badge :value="$audit->status" /></td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $audit->findings ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-on-surface-variant">No audit records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end">
        @can('assets.delete')
            <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Delete asset {{ $asset->asset_code }}? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Delete Asset
                </button>
            </form>
        @endcan
    </div>
</x-app-layout>
