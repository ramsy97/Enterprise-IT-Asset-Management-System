<x-app-layout>
    <x-slot name="title">Maintenance #{{ $record->id }}</x-slot>

    <x-page-header
        :title="'Maintenance ' . $record->type->label()"
        :subtitle="$record->asset?->asset_code . ' · scheduled ' . $record->scheduled_date?->format('d M Y')">

        <x-slot name="actions">
            @can('maintenance.update')
                @if (in_array($record->status->value, ['scheduled', 'in_progress']))
                    <form method="POST" action="{{ route('maintenance.complete', $record) }}" onsubmit="return confirm('Mark this maintenance as completed?')" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            Mark Completed
                        </button>
                    </form>
                    <form method="POST" action="{{ route('maintenance.cancel', $record) }}" onsubmit="return confirm('Cancel this maintenance record?')" class="inline">
                        @csrf
                        <button type="submit" class="btn-secondary">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                            Cancel
                        </button>
                    </form>
                @endif
                <a href="{{ route('maintenance.edit', $record) }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Asset">
            <a href="{{ route('assets.show', $record->asset) }}" class="font-mono text-headline-md font-semibold text-secondary hover:underline">{{ $record->asset?->asset_code }}</a>
            <p class="mt-1 text-body-md text-on-surface">{{ $record->asset?->asset_name }}</p>
            <div class="mt-3"><x-status-badge :value="$record->asset?->status" /></div>
        </x-card>

        <x-card title="Maintenance">
            <dl class="space-y-4">
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Type</dt>
                    <dd class="mt-1 text-body-md font-medium text-on-surface">{{ $record->type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Status</dt>
                    <dd class="mt-1"><x-status-badge :value="$record->status" /></dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Scheduled Date</dt>
                    <dd class="mt-1 text-body-md text-on-surface">{{ $record->scheduled_date?->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Completed Date</dt>
                    <dd class="mt-1 text-body-md text-on-surface">{{ $record->completed_date?->format('d M Y') ?? '—' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Details">
            <dl class="space-y-4">
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Technician</dt>
                    <dd class="mt-1 text-body-md text-on-surface">{{ $record->technician?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Cost</dt>
                    <dd class="mt-1 font-mono text-body-md text-on-surface">{{ rupiah($record->cost) }}</dd>
                </div>
                @if ($record->description)
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Description</dt>
                        <dd class="mt-1 text-body-md text-on-surface">{{ $record->description }}</dd>
                    </div>
                @endif
                @if ($record->result)
                    <div>
                        <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Result</dt>
                        <dd class="mt-1 text-body-md text-on-surface">{{ $record->result }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>
    </div>

    @can('maintenance.delete')
        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('maintenance.destroy', $record) }}" onsubmit="return confirm('Delete this maintenance record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Delete Record
                </button>
            </form>
        </div>
    @endcan
</x-app-layout>
