<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AuditRecord;
use App\Models\MaintenanceRecord;
use App\Models\SoftwareLicense;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->flushDashboardCacheOnDataChanges();
    }

    private function flushDashboardCacheOnDataChanges(): void
    {
        $models = [
            Asset::class,
            AssetAssignment::class,
            AuditRecord::class,
            MaintenanceRecord::class,
            SoftwareLicense::class,
        ];

        foreach ($models as $model) {
            foreach (['created', 'updated', 'deleted', 'restored'] as $event) {
                Event::listen("eloquent.{$event}: {$model}", function () {
                    if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                        Cache::tags(['dashboard'])->flush();
                    }
                });
            }
        }
    }
}
