<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(private readonly MaintenanceService $maintenanceService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaintenanceRecord::class);

        $records = MaintenanceRecord::with(['asset', 'technician'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->when($request->month, function ($q, $v) {
                $month = Carbon::parse($v);
                $q->whereBetween('scheduled_date', [$month->startOfMonth()->toDateString(), $month->endOfMonth()->toDateString()]);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('maintenance.index', [
            'records' => $records,
            'filters' => $request->only(['status', 'type', 'month']),
        ]);
    }

    public function calendar(): View
    {
        $this->authorize('viewAny', MaintenanceRecord::class);

        $events = MaintenanceRecord::with(['asset', 'technician'])->get()->map(fn ($r) => [
            'id' => $r->id,
            'title' => $r->asset?->asset_code.' — '.$r->type->label(),
            'start' => $r->scheduled_date->format('Y-m-d'),
            'status' => $r->status->value,
            'cost' => (float) $r->cost,
            'url' => route('maintenance.show', $r),
        ]);

        return view('maintenance.calendar', ['events' => $events]);
    }

    public function create(): View
    {
        $this->authorize('create', MaintenanceRecord::class);

        return view('maintenance.create', [
            'assets' => Asset::with('category')->where('status', '!=', 'retired')->orderBy('asset_code')->get(),
            'technicians' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $this->authorize('create', MaintenanceRecord::class);

        $record = new MaintenanceRecord($request->validated());
        $this->maintenanceService->create($record);

        return redirect()
            ->route('maintenance.show', $record)
            ->with('success', 'Maintenance record created.');
    }

    public function show(MaintenanceRecord $maintenance): View
    {
        $this->authorize('viewAny', MaintenanceRecord::class);

        $maintenance->load(['asset', 'technician']);

        return view('maintenance.show', ['record' => $maintenance]);
    }

    public function edit(MaintenanceRecord $maintenance): View
    {
        $this->authorize('update', $maintenance);

        return view('maintenance.edit', [
            'record' => $maintenance,
            'assets' => Asset::with('category')->where('status', '!=', 'retired')->orderBy('asset_code')->get(),
            'technicians' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMaintenanceRequest $request, MaintenanceRecord $maintenance): RedirectResponse
    {
        $this->authorize('update', $maintenance);

        $this->maintenanceService->update($maintenance, $request->validated());

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance record updated.');
    }

    public function complete(Request $request, MaintenanceRecord $maintenance): RedirectResponse
    {
        $this->authorize('update', $maintenance);

        $request->validate([
            'cost' => ['nullable', 'numeric', 'min:0'],
            'result' => ['nullable', 'string', 'max:1000'],
            'completed_date' => ['nullable', 'date'],
        ]);

        $this->maintenanceService->complete($maintenance, $request->only(['cost', 'result', 'completed_date']));

        return back()->with('success', 'Maintenance marked as completed.');
    }

    public function cancel(MaintenanceRecord $maintenance): RedirectResponse
    {
        $this->authorize('update', $maintenance);

        $this->maintenanceService->cancel($maintenance);

        return back()->with('success', 'Maintenance cancelled.');
    }

    public function destroy(MaintenanceRecord $maintenance): RedirectResponse
    {
        $this->authorize('delete', $maintenance);

        $this->maintenanceService->delete($maintenance);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance record deleted.');
    }
}
