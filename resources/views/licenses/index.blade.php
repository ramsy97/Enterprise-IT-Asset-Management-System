<x-app-layout>
    <x-slot name="title">Software Licenses</x-slot>

    <x-page-header
        title="Software License"
        subtitle="Track software licenses, seat usage, and renewals.">

        <x-slot name="actions">
            @can('licenses.create')
                <a href="{{ route('licenses.create') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Add License
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Software</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Vendor</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Seats Used</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Availability</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Cost</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Expires</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($licenses as $license)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 font-semibold text-on-surface">{{ $license->software_name }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $license->vendor ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-mono">{{ $license->used_licenses }} / {{ $license->total_licenses }}</span>
                                <div class="mt-1 h-1.5 w-32 overflow-hidden rounded-full bg-surface-container">
                                    <div class="h-full rounded-full bg-secondary" style="width: {{ $license->getUsagePercent() }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($license->getAvailableLicenses() > 0)
                                    <span class="badge badge-green">{{ $license->getAvailableLicenses() }} available</span>
                                @else
                                    <span class="badge badge-red">Fully allocated</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($license->purchase_cost) }}</td>
                            <td class="px-4 py-3">
                                @if ($license->expires_at)
                                    @if ($license->expires_at->isPast())
                                        <span class="badge badge-red">{{ $license->expires_at->format('d M Y') }}</span>
                                    @elseif ($license->expires_at->diffInDays() <= 30)
                                        <span class="badge badge-amber">{{ $license->expires_at->format('d M Y') }}</span>
                                    @else
                                        <span class="text-on-surface">{{ $license->expires_at->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-on-surface-variant">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    @can('licenses.update')
                                        <a href="{{ route('licenses.edit', $license) }}" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary">
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
                                    title="No licenses found"
                                    message="Add your first software license to start tracking.">
                                    @can('licenses.create')
                                        <x-slot name="action">
                                            <a href="{{ route('licenses.create') }}" class="btn-primary">Add License</a>
                                        </x-slot>
                                    @endcan
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($licenses->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $licenses->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
