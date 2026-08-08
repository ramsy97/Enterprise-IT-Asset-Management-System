<?php

namespace App\Http\Controllers;

use App\Enums\AuditStatus;
use App\Http\Requests\StoreAuditRequest;
use App\Http\Requests\UpdateAuditRequest;
use App\Models\Asset;
use App\Models\AuditRecord;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AuditController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditRecord::class);

        $audits = AuditRecord::with(['asset', 'auditor'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->asset_id, fn ($q, $v) => $q->where('asset_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('audits.index', [
            'audits' => $audits,
            'assets' => Asset::orderBy('asset_code')->get(),
            'filters' => $request->only(['status', 'asset_id']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', AuditRecord::class);

        return view('audits.create', [
            'assets' => Asset::with('category', 'location', 'currentHolder')->orderBy('asset_code')->get(),
        ]);
    }

    public function store(StoreAuditRequest $request): RedirectResponse
    {
        $this->authorize('create', AuditRecord::class);

        $audit = $this->auditService->create($request->validated(), $request->file('evidence'));

        return redirect()
            ->route('audits.show', $audit)
            ->with('success', 'Audit record saved.');
    }

    public function show(AuditRecord $audit): View
    {
        $this->authorize('viewAny', AuditRecord::class);

        $audit->load(['asset.category', 'asset.location', 'asset.currentHolder', 'auditor']);

        return view('audits.show', compact('audit'));
    }

    public function evidence(AuditRecord $audit): BinaryFileResponse
    {
        $this->authorize('viewAny', AuditRecord::class);

        abort_unless(Storage::disk('public')->exists($audit->evidence_path), 404);

        return Storage::disk('public')->download($audit->evidence_path);
    }

    public function edit(AuditRecord $audit): View
    {
        $this->authorize('update', $audit);

        return view('audits.edit', [
            'audit' => $audit,
            'assets' => Asset::orderBy('asset_code')->get(),
        ]);
    }

    public function update(UpdateAuditRequest $request, AuditRecord $audit): RedirectResponse
    {
        $this->authorize('update', $audit);

        $this->auditService->update($audit, $request->validated(), $request->file('evidence'));

        return redirect()->route('audits.show', $audit)->with('success', 'Audit record updated.');
    }

    public function verify(Request $request, AuditRecord $audit): RedirectResponse
    {
        $this->authorize('update', $audit);

        $request->validate([
            'status' => ['sometimes', Rule::enum(AuditStatus::class)],
            'findings' => ['nullable', 'string', 'max:1000'],
        ]);

        $audit->update([
            'status' => $request->status ?? 'verified',
            'findings' => $request->findings,
        ]);

        return back()->with('success', 'Audit status updated.');
    }

    public function destroy(AuditRecord $audit): RedirectResponse
    {
        $this->authorize('delete', $audit);

        $this->auditService->delete($audit);

        return redirect()->route('audits.index')->with('success', 'Audit record deleted.');
    }
}
