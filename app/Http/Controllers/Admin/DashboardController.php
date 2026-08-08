<?php

namespace App\Http\Controllers\Admin;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(): View
    {
        return view('dashboard.admin', [
            'kpis' => $this->dashboardService->kpis(),
            'distribution' => $this->dashboardService->assetDistribution(),
            'status' => $this->dashboardService->assetStatus(),
            'maintenanceTrend' => $this->dashboardService->maintenanceTrend(),
            'warrantyTimeline' => $this->dashboardService->warrantyTimeline(),
            'licenses' => $this->dashboardService->licenseUsage(),
            'pendingApprovals' => $this->dashboardService->pendingApprovals(),
            'activities' => $this->dashboardService->recentActivities(),
            'recentAssets' => $this->dashboardService->recentAssets(),
            'upcomingMaintenance' => $this->dashboardService->upcomingMaintenance(),
        ]);
    }
}
