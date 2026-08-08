<?php

namespace App\Http\Controllers\Staff;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(): View
    {
        return view('dashboard.staff', [
            'kpis' => $this->dashboardService->kpis(),
            'status' => $this->dashboardService->assetStatus(),
            'maintenanceTrend' => $this->dashboardService->maintenanceTrend(),
            'warrantyTimeline' => $this->dashboardService->warrantyTimeline(),
            'upcomingMaintenance' => $this->dashboardService->upcomingMaintenance(),
            'recentAssets' => $this->dashboardService->recentAssets(),
        ]);
    }
}
