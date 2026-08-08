<x-app-layout>
    <x-slot name="title">License Report</x-slot>

    <x-page-header
        title="License Report"
        subtitle="License inventory and seat usage.">

        <x-slot name="actions">
            <a href="{{ route('reports.licenses.excel') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">table_view</span>
                Excel
            </a>
            <a href="{{ route('reports.licenses.pdf') }}" class="btn-secondary">
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
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Software</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Vendor</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Seats</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Used</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Available</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Usage %</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Cost</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Expires</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($licenses as $license)
                        <tr class="transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 font-medium text-on-surface">{{ $license->software_name }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $license->vendor ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ $license->total_licenses }}</td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ $license->used_licenses }}</td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ $license->getAvailableLicenses() }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($license->getUsagePercent() >= 90)
                                    <span class="badge badge-red">{{ $license->getUsagePercent() }}%</span>
                                @elseif ($license->getUsagePercent() >= 70)
                                    <span class="badge badge-amber">{{ $license->getUsagePercent() }}%</span>
                                @else
                                    <span class="badge badge-green">{{ $license->getUsagePercent() }}%</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($license->purchase_cost) }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $license->expires_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state title="No licenses found" message="No software licenses registered yet." />
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
