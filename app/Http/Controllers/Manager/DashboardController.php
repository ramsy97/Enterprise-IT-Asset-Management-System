<?php

namespace App\Http\Controllers\Manager;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(): View
    {
        return view('dashboard.manager', [
            'kpis' => $this->dashboardService->kpis(),
            'distribution' => $this->dashboardService->assetDistribution(),
            'status' => $this->dashboardService->assetStatus(),
            'maintenanceTrend' => $this->dashboardService->maintenanceTrend(),
            'warrantyTimeline' => $this->dashboardService->warrantyTimeline(),
            'licenses' => $this->dashboardService->licenseUsage(),
            'pendingApprovals' => $this->dashboardService->pendingApprovals(),
            'recentActivities' => $this->dashboardService->recentActivities(),
        ]);
    }
}
