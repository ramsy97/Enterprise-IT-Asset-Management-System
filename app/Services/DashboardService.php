<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\MaintenanceRecord;
use App\Models\SoftwareLicense;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const TTL = 300;

    public function kpis(): array
    {
        return $this->remember('dashboard.kpis', function () {
            $totalAssets = Asset::count();
            $maintenanceMonth = MaintenanceRecord::whereBetween('scheduled_date', [
                now()->subMonth()->startOfMonth(),
                now()->startOfMonth()->subDay(),
            ])->count();

            return [
                'total_assets' => $totalAssets,
                'total_assets_trend' => $this->monthOverMonth(Asset::class),
                'active_assets' => Asset::whereIn('status', ['available', 'assigned'])->count(),
                'active_assets_trend' => $this->monthOverMonth(Asset::class, activeOnly: true),
                'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
                'maintenance_records_month' => $maintenanceMonth,
                'warranty_expiring' => Asset::whereNotNull('warranty_expires_at')
                    ->where('status', '!=', 'retired')
                    ->where('warranty_expires_at', '<=', now()->addDays(30)->toDateString())
                    ->count(),
                'total_value' => Asset::sum('purchase_price'),
            ];
        });
    }

    public function assetDistribution(): array
    {
        return $this->remember('dashboard.distribution', function () {
            return Asset::selectRaw('asset_categories.name, COUNT(assets.id) as total')
                ->join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
                ->groupBy('asset_categories.name')
                ->orderByDesc('total')
                ->pluck('total', 'name')
                ->toArray();
        });
    }

    public function assetStatus(): array
    {
        return $this->remember('dashboard.status', function () {
            return Asset::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        });
    }

    public function maintenanceTrend(int $months = 12): array
    {
        return $this->remember('dashboard.maintenance.'.$months, function () use ($months) {
            $labels = [];
            $values = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $values[] = MaintenanceRecord::whereYear('scheduled_date', $month->year)
                    ->whereMonth('scheduled_date', $month->month)
                    ->count();
            }

            return ['labels' => $labels, 'values' => $values];
        });
    }

    public function warrantyTimeline(int $days = 90): array
    {
        return $this->remember('dashboard.warranty.'.$days, function () use ($days) {
            return Asset::with('category')
                ->whereNotNull('warranty_expires_at')
                ->where('status', '!=', 'retired')
                ->whereBetween('warranty_expires_at', [now()->toDateString(), now()->addDays($days)->toDateString()])
                ->orderBy('warranty_expires_at')
                ->limit(8)
                ->get()
                ->map(fn ($asset) => [
                    'asset_code' => $asset->asset_code,
                    'asset_name' => $asset->asset_name,
                    'category' => $asset->category?->name,
                    'days_left' => $asset->warrantyDaysLeft(),
                    'expires_at' => $asset->warranty_expires_at?->format('d M Y'),
                ])
                ->all();
        });
    }

    public function licenseUsage(): array
    {
        return $this->remember('dashboard.licenses', function () {
            return [
                'total' => SoftwareLicense::sum('total_licenses'),
                'used' => SoftwareLicense::sum('used_licenses'),
                'available' => max(0, SoftwareLicense::sum('total_licenses') - SoftwareLicense::sum('used_licenses')),
                'top' => SoftwareLicense::orderByDesc('used_licenses')->limit(5)->get()
                    ->map(fn ($l) => [
                        'name' => $l->software_name,
                        'used' => $l->used_licenses,
                        'total' => $l->total_licenses,
                        'percent' => $l->getUsagePercent(),
                    ]),
            ];
        });
    }

    public function pendingApprovals(): int
    {
        return AssetAssignment::where('status', 'pending')->count();
    }

    public function recentActivities(int $limit = 10): Collection
    {
        return ActivityLog::with('user')->latest()->limit($limit)->get();
    }

    public function recentAssets(int $limit = 6): Collection
    {
        return Asset::with('category', 'location', 'currentHolder')->latest()->limit($limit)->get();
    }

    public function upcomingMaintenance(int $limit = 6): Collection
    {
        return MaintenanceRecord::with('asset', 'technician')
            ->where('status', '!=', 'completed')
            ->orderBy('scheduled_date')
            ->limit($limit)
            ->get();
    }

    private function remember(string $key, callable $callback): mixed
    {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags(['dashboard'])->remember($key, self::TTL, $callback);
        }

        return Cache::remember($key, self::TTL, $callback);
    }

    private function monthOverMonth(string $model, bool $activeOnly = false): int
    {
        $base = $model::query();

        if ($activeOnly) {
            $base->whereIn('status', ['available', 'assigned']);
        }

        $thisMonth = (clone $base)->where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = (clone $base)->whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->startOfMonth()->subSecond(),
        ])->count();

        if ($lastMonth === 0) {
            return $thisMonth > 0 ? 100 : 0;
        }

        return (int) round(($thisMonth - $lastMonth) / $lastMonth * 100);
    }
}
