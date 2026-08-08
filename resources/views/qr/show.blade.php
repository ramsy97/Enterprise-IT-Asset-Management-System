<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $asset->asset_code }} — ITAMS</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background">
        <div class="mx-auto flex max-w-3xl flex-col items-center px-6 py-12">
            <div class="mb-8 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-sm font-bold text-white">IE</span>
                <div>
                    <h1 class="text-headline-md font-bold leading-6 text-on-surface">ITAMS Enterprise</h1>
                    <p class="text-label-md text-on-surface-variant">Asset Verification</p>
                </div>
            </div>

            <div class="card w-full overflow-hidden">
                <div class="border-b border-outline-variant/70 bg-primary-container px-6 py-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-mono text-body-sm text-on-primary-container">{{ $asset->category?->name ?? 'Asset' }}</p>
                            <h2 class="mt-0.5 font-mono text-headline-lg font-semibold text-white">{{ $asset->asset_code }}</h2>
                        </div>
                        <x-status-badge :value="$asset->status" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-[200px_1fr]">
                    <div class="flex flex-col items-center">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4">
                            <img src="{{ route('qr.image', $asset) }}" alt="QR code for {{ $asset->asset_code }}" class="h-40 w-40">
                        </div>
                        <p class="mt-3 text-body-sm text-on-surface-variant">Scan to verify this asset</p>
                    </div>

                    <div>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Asset Name</dt>
                                <dd class="mt-1 text-body-md font-medium text-on-surface">{{ $asset->asset_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Brand / Model</dt>
                                <dd class="mt-1 text-body-md font-medium text-on-surface">{{ $asset->brand }} {{ $asset->model }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Serial Number</dt>
                                <dd class="mt-1 font-mono text-body-md text-on-surface">{{ $asset->serial_number ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Location</dt>
                                <dd class="mt-1 text-body-md text-on-surface">{{ $asset->location?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Current Holder</dt>
                                <dd class="mt-1 text-body-md text-on-surface">{{ $asset->currentHolder?->name ?? 'Unassigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Purchase Date</dt>
                                <dd class="mt-1 text-body-md text-on-surface">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Warranty Expiry</dt>
                                <dd class="mt-1 text-body-md text-on-surface">{{ $asset->warranty_expires_at?->format('d M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-label-md uppercase tracking-wider text-on-surface-variant">Warranty Status</dt>
                                <dd class="mt-1 text-body-md text-on-surface">
                                    @if ($asset->warranty_expires_at)
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
                        </dl>

                        @if ($asset->notes)
                            <div class="mt-5 rounded-xl bg-surface-container-low p-4">
                                <p class="text-label-md uppercase tracking-wider text-on-surface-variant">Notes</p>
                                <p class="mt-1 text-body-md text-on-surface">{{ $asset->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <p class="mt-8 text-body-sm text-on-surface-variant">&copy; {{ date('Y') }} ITAMS Enterprise — Verified via secure QR check-in</p>
        </div>
    </body>
</html>
