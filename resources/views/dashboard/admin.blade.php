<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-page-header
        title="IT Asset Overview"
        subtitle="Monitor company assets, maintenance activities, and compliance status.">

        <x-slot name="actions">
            <a href="{{ route('assets.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span>
                New Asset
            </a>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <x-kpi-card
            label="Total Assets"
            icon="inventory_2"
            :value="number_format($kpis['total_assets'])"
            :trend="$kpis['total_assets_trend']" />

        <x-kpi-card
            label="Active Assets"
            icon="check_circle"
            :value="number_format($kpis['active_assets'])"
            :trend="$kpis['active_assets_trend']" />

        <x-kpi-card
            label="Under Maintenance"
            icon="build"
            :value="number_format($kpis['maintenance_assets'])"
            trendText="assets this month" />

        <x-kpi-card
            label="Warranty Alert"
            icon="warning"
            :value="number_format($kpis['warranty_expiring'])"
            trendText="expired or within 30 days"
            :trendColor="'text-error'"
            trendIcon="warning" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Asset Distribution">
            <div class="relative mx-auto h-56 w-56">
                <canvas id="chart-distribution"></canvas>
            </div>
            <div id="distribution-legend" class="mt-4 flex flex-wrap justify-center gap-3"></div>
        </x-card>

        <x-card title="Asset Status" padding="false">
            <div class="h-64 p-5">
                <canvas id="chart-status"></canvas>
            </div>
        </x-card>

        <x-card title="Maintenance Trend" padding="false">
            <div class="h-64 p-5">
                <canvas id="chart-maintenance"></canvas>
            </div>
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Warranty Timeline" padding="false">
            <div class="h-64 p-5">
                <canvas id="chart-warranty"></canvas>
            </div>
        </x-card>

        <x-card title="Software License Usage" padding="false">
            <div class="h-64 p-5">
                <canvas id="chart-licenses"></canvas>
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card title="Pending Approvals">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </span>
                    <div>
                        <p class="text-headline-lg text-on-surface">{{ $pendingApprovals }}</p>
                        <p class="text-body-sm text-on-surface-variant">requests awaiting approval</p>
                    </div>
                    <a href="{{ route('assignments.index', ['status' => 'pending']) }}" class="ml-auto text-body-sm font-semibold text-secondary hover:underline">Review</a>
                </div>
            </x-card>

            <x-card title="Asset Value">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-secondary-fixed text-on-secondary-fixed-variant">
                        <span class="material-symbols-outlined">payments</span>
                    </span>
                    <div>
                        <p class="text-headline-lg text-on-surface">{{ rupiah($kpis['total_value']) }}</p>
                        <p class="text-body-sm text-on-surface-variant">total portfolio value</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Recent Assets">
            <ul class="divide-y divide-surface-variant/60">
                @forelse ($recentAssets as $asset)
                    <li class="flex items-center gap-3 py-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-container font-mono text-xs font-medium text-on-surface-variant">{{ $asset->category?->code_prefix ?? 'IT' }}</span>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('assets.show', $asset) }}" class="block truncate text-body-md font-medium text-on-surface hover:text-secondary">{{ $asset->asset_name }}</a>
                            <p class="font-mono text-body-sm text-on-surface-variant">{{ $asset->asset_code }}</p>
                        </div>
                        <x-status-badge :value="$asset->status" />
                    </li>
                @empty
                    <li class="py-8 text-center text-body-sm text-on-surface-variant">No assets yet.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Upcoming Maintenance">
            <ul class="divide-y divide-surface-variant/60">
                @forelse ($upcomingMaintenance as $record)
                    <li class="flex items-center gap-3 py-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600">
                            <span class="material-symbols-outlined text-[18px]">build</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-body-md font-medium text-on-surface">{{ $record->asset?->asset_name }}</p>
                            <p class="text-body-sm text-on-surface-variant">{{ $record->scheduled_date?->format('d M Y') }} · {{ $record->type->label() }}</p>
                        </div>
                        <x-status-badge :value="$record->status" />
                    </li>
                @empty
                    <li class="py-8 text-center text-body-sm text-on-surface-variant">No upcoming maintenance.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Recent Activity">
            <ul class="divide-y divide-surface-variant/60">
                @forelse ($activities as $activity)
                    <li class="py-3">
                        <p class="text-body-md text-on-surface">{{ $activity->description }}</p>
                        <p class="mt-0.5 text-body-sm text-on-surface-variant">
                            {{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at?->diffForHumans() }}
                        </p>
                    </li>
                @empty
                    <li class="py-8 text-center text-body-sm text-on-surface-variant">No activity yet.</li>
                @endforelse
            </ul>
        </x-card>
    </div>

    @push('scripts')
    <script type="module">
        const distributionData = @json($distribution);
        const statusData = @json($status);
        const maintenanceData = @json($maintenanceTrend);
        const warrantyData = @json($warrantyTimeline);
        const licenseData = @json($licenses);

        const C = window.CHART_COLORS;

        const distributionValues = Object.values(distributionData);
        const distributionKeys = Object.keys(distributionData);
        const distPalette = ['#0058be', '#2170e4', '#131b2e', '#10b981', '#f59e0b', '#ba1a1a', '#8b5cf6', '#06b6d4', '#c6c6cd'];

        const distChart = initChart('chart-distribution', {
            type: 'doughnut',
            data: {
                labels: distributionKeys,
                datasets: [{
                    data: distributionValues,
                    backgroundColor: distPalette,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw} assets`,
                        },
                    },
                },
            },
        });

        const legendEl = document.getElementById('distribution-legend');
        if (legendEl) {
            distributionKeys.forEach((label, i) => {
                const span = document.createElement('span');
                span.className = 'inline-flex items-center gap-1.5 text-label-md text-on-surface-variant';
                span.innerHTML = `<span class="h-2.5 w-2.5 rounded-full" style="background:${distPalette[i % distPalette.length]}"></span>${label}`;
                legendEl.appendChild(span);
            });
        }

        initChart('chart-status', {
            type: 'bar',
            data: {
                labels: Object.keys(statusData).map((s) => s.replace('_', ' ')),
                datasets: [{
                    label: 'Assets',
                    data: Object.values(statusData),
                    backgroundColor: ['#2170e4', '#10b981', '#f59e0b', '#ba1a1a'],
                    borderRadius: 6,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eceef0' } },
                    x: { grid: { display: false } },
                },
            },
        });

        initChart('chart-maintenance', {
            type: 'line',
            data: {
                labels: maintenanceData.labels,
                datasets: [{
                    label: 'Maintenance records',
                    data: maintenanceData.values,
                    borderColor: C.secondary,
                    backgroundColor: 'rgba(0, 88, 190, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: C.secondary,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eceef0' } },
                    x: { grid: { display: false } },
                },
            },
        });

        initChart('chart-warranty', {
            type: 'bar',
            data: {
                labels: warrantyData.map((w) => w.asset_code),
                datasets: [{
                    label: 'Days left',
                    data: warrantyData.map((w) => w.days_left),
                    backgroundColor: warrantyData.map((w) => (w.days_left <= 30 ? C.red : w.days_left <= 60 ? C.amber : C.green)),
                    borderRadius: 6,
                    maxBarThickness: 34,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw} days (${warrantyData[ctx.dataIndex].expires_at})`,
                        },
                    },
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eceef0' } },
                    y: { grid: { display: false } },
                },
            },
        });

        initChart('chart-licenses', {
            type: 'bar',
            data: {
                labels: licenseData.top.map((l) => l.name),
                datasets: [{
                    label: 'Used',
                    data: licenseData.top.map((l) => l.used),
                    backgroundColor: C.secondary,
                    borderRadius: 6,
                    maxBarThickness: 28,
                }, {
                    label: 'Available',
                    data: licenseData.top.map((l) => l.total - l.used),
                    backgroundColor: C.gray,
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eceef0' } },
                    x: { grid: { display: false } },
                },
            },
        });
    </script>
    @endpush
</x-app-layout>
