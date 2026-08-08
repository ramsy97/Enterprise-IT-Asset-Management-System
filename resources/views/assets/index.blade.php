<x-app-layout>
    <x-slot name="title">Asset Management</x-slot>

    <x-page-header
        title="Asset Management"
        subtitle="Register, track, and manage your company's IT assets.">

        <x-slot name="actions">
            @can('assets.create')
                <a href="{{ route('assets.create') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Register Asset
                </a>
            @endcan
        </x-slot>
    </x-page-header>

    <x-card padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-outline-variant/70 p-4">
            <form method="GET" action="{{ route('assets.index') }}" class="flex w-full flex-wrap items-end gap-3">
                <div class="min-w-[220px] flex-1">
                    <label for="search" class="label-field">Search</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/60">search</span>
                        <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Asset code, name, serial, holder..."
                               class="input-field pl-10">
                    </div>
                </div>
                <div class="w-40">
                    <label for="category_id" class="label-field">Category</label>
                    <select id="category_id" name="category_id" class="input-field">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
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
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary">Apply</button>
                    @if (($filters['search'] ?? null) || ($filters['category_id'] ?? null) || ($filters['location_id'] ?? null) || ($filters['status'] ?? null))
                        <a href="{{ route('assets.index') }}" class="btn-secondary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] border-collapse text-left">
                <thead class="sticky top-0 z-10 border-b border-outline-variant bg-[#F1F5F9]">
                    <tr>
                        <th class="px-4 py-3 text-label-md text-on-surface">QR</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset ID</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Asset Name</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Category</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Serial Number</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Assigned User</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Location</th>
                        <th class="px-4 py-3 text-label-md text-on-surface">Status</th>
                        <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                    @forelse ($assets as $asset)
                        <tr class="group transition-colors hover:bg-[#F8FAFC]">
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('qr.show', $asset) }}" title="View QR code" class="inline-flex text-on-surface-variant hover:text-secondary">
                                    <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono text-mono">
                                <a href="{{ route('assets.show', $asset) }}" class="hover:text-secondary">{{ $asset->asset_code }}</a>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $asset->asset_name }}</td>
                            <td class="px-4 py-3">{{ $asset->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-mono text-on-surface-variant">{{ $asset->serial_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($asset->currentHolder)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-secondary/10 text-[10px] font-bold text-secondary">{{ $asset->currentHolder->initials() }}</span>
                                        {{ $asset->currentHolder->name }}
                                    </span>
                                @else
                                    <span class="italic text-on-surface-variant">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $asset->location?->name ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :value="$asset->status" /></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <a href="{{ route('assets.show', $asset) }}" title="View" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    @can('assets.update')
                                        <a href="{{ route('assets.edit', $asset) }}" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-empty-state
                                    title="No assets found"
                                    message="No assets match your filters. Try clearing them or register a new asset.">
                                    @can('assets.create')
                                        <x-slot name="action">
                                            <a href="{{ route('assets.create') }}" class="btn-primary">Register Asset</a>
                                        </x-slot>
                                    @endcan
                                </x-empty-state>
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
