<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLocationController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route(auth()->user()->homeRoute()) : redirect()->route('login');
});

Route::get('/cron/{token}', function (string $token) {
    $expected = (string) env('CRON_TOKEN', '');

    abort_unless($expected !== '' && hash_equals($expected, $token), 403);

    Artisan::call('schedule:run');

    return response('ok');
})->name('cron');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/dashboard', function () {
        $home = auth()->user()->homeRoute();

        return $home === 'dashboard'
            ? view('dashboard')
            : redirect()->route($home);
    })->name('dashboard');

    // ---------- Profile ----------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/qr/{asset:asset_code}', [QrController::class, 'show'])
        ->withoutMiddleware('auth')
        ->name('qr.show');

    Route::get('/qr/{asset:asset_code}/image', [QrController::class, 'image'])
        ->withoutMiddleware('auth')
        ->name('qr.image');

    // ---------- Assets ----------
    Route::resource('assets', AssetController::class)
        ->middleware('permission:assets.view|assets.create|assets.update|assets.delete');

    // ---------- Master Data ----------
    Route::middleware('permission:categories.manage')->group(function () {
        Route::resource('categories', AssetCategoryController::class)->except(['show']);
    });

    Route::middleware('permission:locations.manage')->group(function () {
        Route::resource('locations', AssetLocationController::class)->except(['show']);
    });

    // ---------- Assignments ----------
    Route::middleware('permission:assignments.view|assignments.request|assignments.approve')->group(function () {
        Route::get('assignments', [AssetAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/create', [AssetAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments', [AssetAssignmentController::class, 'store'])->name('assignments.store');
        Route::post('assignments/{assignment}/approve', [AssetAssignmentController::class, 'approve'])->name('assignments.approve');
        Route::post('assignments/{assignment}/reject', [AssetAssignmentController::class, 'reject'])->name('assignments.reject');
        Route::post('assignments/{assignment}/return', [AssetAssignmentController::class, 'returnAsset'])->name('assignments.return');
    });

    // ---------- Maintenance ----------
    Route::middleware('permission:maintenance.view|maintenance.create|maintenance.update|maintenance.delete')->group(function () {
        Route::get('maintenance/calendar', [MaintenanceController::class, 'calendar'])->name('maintenance.calendar');
        Route::resource('maintenance', MaintenanceController::class);
        Route::post('maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::post('maintenance/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenance.cancel');
    });

    // ---------- Warranty ----------
    Route::get('warranty', [WarrantyController::class, 'index'])
        ->middleware('permission:warranty.view')
        ->name('warranty.index');

    // ---------- Licenses ----------
    Route::middleware('permission:licenses.view|licenses.create|licenses.update|licenses.delete')->group(function () {
        Route::resource('licenses', LicenseController::class);
    });

    // ---------- Audits ----------
    Route::middleware('permission:audits.view|audits.create|audits.update|audits.delete')->group(function () {
        Route::resource('audits', AuditController::class);
        Route::get('audits/{audit}/evidence', [AuditController::class, 'evidence'])->name('audits.evidence');
        Route::post('audits/{audit}/verify', [AuditController::class, 'verify'])->name('audits.verify');
    });

    // ---------- Reports ----------
    Route::middleware('permission:reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/assets', [ReportController::class, 'assetReport'])->name('assets');
        Route::get('/assets/excel', [ReportController::class, 'assetExcel'])->name('assets.excel');
        Route::get('/assets/pdf', [ReportController::class, 'assetPdf'])->name('assets.pdf');
        Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('maintenance');
        Route::get('/maintenance/excel', [ReportController::class, 'maintenanceExcel'])->name('maintenance.excel');
        Route::get('/maintenance/pdf', [ReportController::class, 'maintenancePdf'])->name('maintenance.pdf');
        Route::get('/audits', [ReportController::class, 'auditReport'])->name('audits');
        Route::get('/audits/excel', [ReportController::class, 'auditExcel'])->name('audits.excel');
        Route::get('/audits/pdf', [ReportController::class, 'auditPdf'])->name('audits.pdf');
        Route::get('/licenses', [ReportController::class, 'licenseReport'])->name('licenses');
        Route::get('/licenses/excel', [ReportController::class, 'licenseExcel'])->name('licenses.excel');
        Route::get('/licenses/pdf', [ReportController::class, 'licensePdf'])->name('licenses.pdf');
    });

    // ---------- Admin ----------
    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // ---------- Role Dashboards ----------
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])
        ->middleware('role:IT STAFF')
        ->name('staff.dashboard');

    Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])
        ->middleware('role:MANAGER')
        ->name('manager.dashboard');
});
