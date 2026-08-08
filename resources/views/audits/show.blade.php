<x-app-layout>
    <x-slot name="title">Audit {{ $audit->audit_batch_id }}</x-slot>

    <x-page-header
        :title="'Audit ' . $audit->audit_batch_id"
        :subtitle="$audit->asset?->asset_code . ' · ' . $audit->audit_date?->format('d M Y')">

        <x-slot name="actions">
            @can('audits.update')
                <a href="{{ route('audits.edit', $audit) }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Asset">
            <a href="{{ route('assets.show', $audit->asset) }}" class="font-mono text-headline-md font-semibold text-secondary hover:underline">{{ $audit->asset?->asset_code }}</a>
            <p class="mt-1 text-body-md text-on-surface">{{ $audit->asset?->asset_name }}</p>
            <p class="mt-1 text-body-sm text-on-surface-variant">{{ $audit->asset?->category?->name }} · {{ $audit->asset?->location?->name }}</p>
            <div class="mt-3"><x-status-badge :value="$audit->asset?->status" /></div>
        </x-card>

        <x-card title="Audit Result">
            <dl class="space-y-4">
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Status</dt>
                    <dd class="mt-1"><x-status-badge :value="$audit->status" /></dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Condition</dt>
                    <dd class="mt-1 text-body-md font-medium text-on-surface">{{ $audit->condition ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Location Match</dt>
                    <dd class="mt-1">
                        @if ($audit->location_match)
                            <span class="badge badge-green">Matched</span>
                        @else
                            <span class="badge badge-red">Mismatch</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Details">
            <dl class="space-y-4">
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Auditor</dt>
                    <dd class="mt-1 text-body-md text-on-surface">{{ $audit->auditor?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Audit Date</dt>
                    <dd class="mt-1 text-body-md text-on-surface">{{ $audit->audit_date?->format('d M Y') }}</dd>
                </div>
                @if ($audit->evidence_path)
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Evidence</dt>
                        <dd class="mt-1">
                            <a href="{{ route('audits.evidence', $audit) }}" target="_blank" class="inline-flex items-center gap-1 text-body-sm font-semibold text-secondary hover:underline">
                                <span class="material-symbols-outlined text-[16px]">image</span>
                                View evidence photo
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </x-card>
    </div>

    @if ($audit->findings)
        <x-card title="Findings" class="mt-6">
            <p class="text-body-md text-on-surface">{{ $audit->findings }}</p>
        </x-card>
    @endif

    @can('audits.update')
        <x-card title="Update Status" class="mt-6">
            <form method="POST" action="{{ route('audits.verify', $audit) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="w-48">
                    <label for="status" class="label-field">New Status</label>
                    <select id="status" name="status" class="input-field">
                        @foreach (\App\Enums\AuditStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($status->value === $audit->status?->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="findings" class="label-field">Findings</label>
                    <input type="text" id="findings" name="findings" value="{{ $audit->findings }}" class="input-field">
                </div>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    Update Audit
                </button>
            </form>
        </x-card>
    @endcan

    @can('audits.delete')
        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('audits.destroy', $audit) }}" onsubmit="return confirm('Delete this audit record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Delete Audit
                </button>
            </form>
        </div>
    @endcan
</x-app-layout>
