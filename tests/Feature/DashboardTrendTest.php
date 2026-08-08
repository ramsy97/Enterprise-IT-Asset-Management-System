<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardTrendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::tags(['dashboard'])->flush();
        $this->travelTo(now()->startOfMonth()->addDays(10));
    }

    public function test_kpi_counts_and_trends_are_computed_from_current_data(): void
    {
        Asset::factory()->count(5)->create([
            'status' => 'available',
            'created_at' => now()->subMonth()->addDays(5),
        ]);

        Asset::factory()->count(10)->create([
            'status' => 'available',
            'created_at' => now()->subDays(2),
        ]);

        Asset::factory()->count(3)->create([
            'status' => 'maintenance',
            'created_at' => now()->subDays(1),
        ]);

        $kpis = app(DashboardService::class)->kpis();

        $this->assertSame(18, $kpis['total_assets']);
        $this->assertSame(15, $kpis['active_assets']);
        $this->assertSame(3, $kpis['maintenance_assets']);

        $this->assertSame(160, $kpis['total_assets_trend']);
        $this->assertSame(100, $kpis['active_assets_trend']);
    }

    public function test_kpi_counts_refresh_after_data_changes(): void
    {
        Asset::factory()->create(['status' => 'available']);

        $before = app(DashboardService::class)->kpis()['total_assets'];

        Asset::factory()->create(['status' => 'available']);

        $this->assertSame($before + 1, app(DashboardService::class)->kpis()['total_assets']);
    }

    public function test_warranty_kpi_includes_expired_and_expiring_soon(): void
    {
        Asset::factory()->count(5)->create([
            'status' => 'available',
            'warranty_expires_at' => now()->subDays(10)->toDateString(),
        ]);

        Asset::factory()->count(3)->create([
            'status' => 'available',
            'warranty_expires_at' => now()->addDays(15)->toDateString(),
        ]);

        Asset::factory()->count(2)->create([
            'status' => 'available',
            'warranty_expires_at' => now()->addDays(60)->toDateString(),
        ]);

        Asset::factory()->create([
            'status' => 'retired',
            'warranty_expires_at' => now()->subDays(5)->toDateString(),
        ]);

        $kpis = app(DashboardService::class)->kpis();

        $this->assertSame(8, $kpis['warranty_expiring']);
    }
}
