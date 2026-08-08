<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Exports\AuditsExport;
use App\Exports\LicensesExport;
use App\Exports\MaintenancesExport;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(): \Illuminate\View\View
    {
        return view('reports.index', [
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => AssetLocation::orderBy('name')->get(),
        ]);
    }

    public function assetReport(Request $request): \Illuminate\View\View
    {
        $filters = $request->only(['status', 'category_id', 'location_id', 'search']);

        return view('reports.assets', [
            'assets' => $this->reportService->assets($filters)->paginate(15)->withQueryString(),
            'filters' => $filters,
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => AssetLocation::orderBy('name')->get(),
        ]);
    }

    public function maintenanceReport(Request $request): \Illuminate\View\View
    {
        $filters = $request->only(['status', 'type', 'from', 'to', 'asset_id']);

        return view('reports.maintenance', [
            'records' => $this->reportService->maintenance($filters)->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function auditReport(Request $request): \Illuminate\View\View
    {
        $filters = $request->only(['status', 'from', 'to']);

        return view('reports.audits', [
            'audits' => $this->reportService->audits($filters)->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function licenseReport(): \Illuminate\View\View
    {
        return view('reports.licenses', [
            'licenses' => $this->reportService->licenses()->paginate(15),
        ]);
    }

    public function assetExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new AssetsExport($request->all()), 'assets-report.xlsx');
    }

    public function maintenanceExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new MaintenancesExport($request->all()), 'maintenance-report.xlsx');
    }

    public function auditExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new AuditsExport($request->all()), 'audits-report.xlsx');
    }

    public function licenseExcel(): BinaryFileResponse
    {
        return Excel::download(new LicensesExport(), 'licenses-report.xlsx');
    }

    public function assetPdf(Request $request): Response
    {
        $assets = $this->reportService->assets($request->all())->get();

        $pdf = Pdf::loadView('pdf.reports.assets', ['assets' => $assets])
            ->setPaper('a4', 'landscape');

        return $pdf->download('assets-report.pdf');
    }

    public function maintenancePdf(Request $request): Response
    {
        $records = $this->reportService->maintenance($request->all())->get();

        $pdf = Pdf::loadView('pdf.reports.maintenance', ['records' => $records])
            ->setPaper('a4', 'landscape');

        return $pdf->download('maintenance-report.pdf');
    }

    public function auditPdf(Request $request): Response
    {
        $audits = $this->reportService->audits($request->all())->get();

        $pdf = Pdf::loadView('pdf.reports.audits', ['audits' => $audits])
            ->setPaper('a4', 'landscape');

        return $pdf->download('audits-report.pdf');
    }

    public function licensePdf(): Response
    {
        $licenses = $this->reportService->licenses()->get();

        $pdf = Pdf::loadView('pdf.reports.licenses', ['licenses' => $licenses])
            ->setPaper('a4', 'landscape');

        return $pdf->download('licenses-report.pdf');
    }
}
