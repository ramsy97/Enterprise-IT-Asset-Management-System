<x-app-layout>
    <x-slot name="title">Asset Assignment</x-slot>

    <x-page-header
        title="Asset Assignment"
        subtitle="Request, approve, and track asset assignments.">

        <x-slot name="actions">
            @can('assignments.request')
                <a href="{{ route('assignments.create') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    New Assignment
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-outline-variant/70 p-4">
            <form method="GET" action="{{ route('assignments.index') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="w-48">
                    <label for="status" class="label-field">Status</label>
                    <select id="status" name="status" class="input-field" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach (\App\Enums\AssignmentStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($filters['status'] ?? null)
                    <a href="{{ route('assignments.index') }}" class="btn-secondary">
                        <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Employee</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Requested</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Approved By</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($assignments as $assignment)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3">
                                <a href="{{ route('assets.show', $assignment->asset) }}" class="font-mono text-mono hover:text-secondary">{{ $assignment->asset?->asset_code }}</a>
                                <p class="text-body-sm text-on-surface-variant">{{ $assignment->asset?->asset_name }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-2">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-secondary/10 text-[10px] font-bold text-secondary">{{ $assignment->employee?->initials() }}</span>
                                    {{ $assignment->employee?->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $assignment->request_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$assignment->status" /></td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $assignment->approver?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    @can('assignments.approve')
                                        @if ($assignment->status->value === 'pending')
                                            <form method="POST" action="{{ route('assignments.approve', $assignment) }}">
                                                @csrf
                                                <button type="submit" title="Approve" class="rounded p-1 text-[#067a4f] hover:bg-[#10b981]/10">
                                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('assignments.reject', $assignment) }}" onsubmit="return confirm('Reject this assignment request?')">
                                                @csrf
                                                <button type="submit" title="Reject" class="rounded p-1 text-error hover:bg-error/10">
                                                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('assignments.return')
                                        @if ($assignment->status->value === 'approved')
                                            <form method="POST" action="{{ route('assignments.return', $assignment) }}" onsubmit="return confirm('Mark this asset as returned?')">
                                                @csrf
                                                <button type="submit" title="Mark returned" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                                    <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    title="No assignments found"
                                    message="No asset assignments match your filters." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($assignments->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $assignments->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
