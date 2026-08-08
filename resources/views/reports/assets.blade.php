<x-app-layout>
    <x-slot name="title">Asset Report</x-slot>

    <x-page-header
        title="Asset Report"
        subtitle="Filter and export the full asset inventory.">

        <x-slot name="actions">
            <a href="{{ route('reports.assets.excel', $filters) }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">table_view</span>
                Excel
            </a>
            <a href="{{ route('reports.assets.pdf', $filters) }}" class="btn-secondary">
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
            <form method="GET" action="{{ route('reports.assets') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="min-w-[200px] flex-1">
                    <label for="search" class="label-field">Search</label>
                    <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="input-field" placeholder="Asset code, name, holder...">
                </div>
                <div class="w-44">
                    <label for="category_id" class="label-field">Category</label>
                    <select id="category_id" name="category_id" class="input-field">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-44">
                    <label for="location_id" class="label-field">Location</label>
                    <select id="location_id" name="location_id" class="input-field">
                        <option value="">All</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(($filters['location_id'] ?? null) == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label for="status" class="label-field">Status</label>
                    <select id="status" name="status" class="input-field">
                        <option value="">All</option>
                        @foreach (\App\Enums\AssetStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Apply</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset ID</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset Name</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Category</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Serial</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Holder</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Location</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-right text-label-md text-on-surface">Price</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Warranty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($assets as $asset)
                        <tr class="transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 font-mono text-mono">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $asset->asset_name }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $asset->category?->name }}</td>
                            <td class="px-4 py-3 font-mono text-mono text-on-surface-variant">{{ $asset->serial_number ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $asset->currentHolder?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $asset->location?->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$asset->status" /></td>
                            <td class="px-4 py-3 text-right font-mono text-on-surface">{{ rupiah($asset->purchase_price) }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $asset->warranty_expires_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-empty-state title="No assets found" message="No assets match the selected filters." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($assets->hasPages())
            <div class="border-t border-outline-variant/70 px-4 py-3">
                {{ $assets->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
