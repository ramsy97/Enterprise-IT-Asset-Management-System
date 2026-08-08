<x-app-layout>
    <x-slot name="title">Warranty</x-slot>

    <x-page-header
        title="Warranty Management"
        subtitle="Monitor warranty coverage and expiry across the asset portfolio." />

    <x-card title="Warranty Filter" class="mb-6">
        <form method="GET" action="{{ route('warranty.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="w-64">
                <label for="days" class="label-field">Expiring within (days)</label>
                <select id="days" name="days" class="input-field">
                    @foreach ([30, 60, 90, 180, 365] as $value)
                        <option value="{{ $value }}" @selected($days === $value)>{{ $value }} days</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Apply</button>
        </form>
    </x-card>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card title="Expiring Soon" :subtitle="'Warranty expiring within the next ' . $days . ' days'">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Asset</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Category</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Expires</th>
                            <th class="px-4 py-3 text-right text-label-md text-on-surface-variant">Days Left</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm">
                        @forelse ($expiring as $asset)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3">
                                    <a href="{{ route('assets.show', $asset) }}" class="font-mono text-mono font-medium text-on-surface hover:text-secondary">{{ $asset->asset_code }}</a>
                                    <p class="text-body-sm text-on-surface-variant">{{ $asset->asset_name }}</p>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $asset->category?->name }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $asset->warranty_expires_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($asset->warrantyDaysLeft() <= 30)
                                        <span class="badge badge-red">{{ $asset->warrantyDaysLeft() }} days</span>
                                    @elseif ($asset->warrantyDaysLeft() <= 60)
                                        <span class="badge badge-amber">{{ $asset->warrantyDaysLeft() }} days</span>
                                    @else
                                        <span class="badge badge-green">{{ $asset->warrantyDaysLeft() }} days</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state
                                        title="No warranties expiring"
                                        message="No assets have warranties expiring within the selected window." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Expired Warranties">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-surface-container-low">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Asset</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Category</th>
                            <th class="px-4 py-3 text-label-md text-on-surface-variant">Expired</th>
                            <th class="px-4 py-3 text-right text-label-md text-on-surface-variant">Days Ago</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm">
                        @forelse ($expired as $asset)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3">
                                    <a href="{{ route('assets.show', $asset) }}" class="font-mono text-mono font-medium text-on-surface hover:text-secondary">{{ $asset->asset_code }}</a>
                                    <p class="text-body-sm text-on-surface-variant">{{ $asset->asset_name }}</p>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $asset->category?->name }}</td>
                                <td class="px-4 py-3 text-on-surface">{{ $asset->warranty_expires_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right"><span class="badge badge-red">{{ abs($asset->warrantyDaysLeft()) }} days</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state
                                        title="No expired warranties"
                                        message="All asset warranties are currently active." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
