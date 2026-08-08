<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Jobs\SendAssignmentNotificationJob;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Services\AssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetAssignmentController extends Controller
{
    public function __construct(private readonly AssignmentService $assignmentService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', AssetAssignment::class);

        $assignments = AssetAssignment::with(['asset', 'employee', 'approver'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('assignments.index', [
            'assignments' => $assignments,
            'filters' => $request->only(['status']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', AssetAssignment::class);

        return view('assignments.create', [
            'assets' => Asset::where('status', 'available')->with('category', 'location')->orderBy('asset_code')->get(),
            'employees' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', AssetAssignment::class);

        $assignment = new AssetAssignment([
            'asset_id' => $request->asset_id,
            'employee_id' => $request->employee_id,
            'assigned_by' => auth()->id(),
            'request_date' => $request->request_date ?? now()->toDateString(),
            'notes' => $request->notes,
        ]);

        $assignment = $this->assignmentService->create($assignment);

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Assignment request submitted for approval.');
    }

    public function approve(Request $request, AssetAssignment $assignment): RedirectResponse
    {
        $this->authorize('approve', $assignment);

        try {
            $this->assignmentService->approve($assignment, $request->user());
            SendAssignmentNotificationJob::dispatch($assignment, 'approved');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Assignment for {$assignment->asset->asset_code} approved and asset assigned.");
    }

    public function reject(Request $request, AssetAssignment $assignment): RedirectResponse
    {
        $this->authorize('reject', $assignment);

        $this->assignmentService->reject($assignment, $request->user());
        SendAssignmentNotificationJob::dispatch($assignment, 'rejected');

        return back()->with('success', "Assignment for {$assignment->asset->asset_code} rejected.");
    }

    public function returnAsset(Request $request, AssetAssignment $assignment): RedirectResponse
    {
        $this->authorize('returnAsset', $assignment);

        $this->assignmentService->returnAsset($assignment);
        SendAssignmentNotificationJob::dispatch($assignment, 'returned');

        return back()->with('success', "Asset {$assignment->asset->asset_code} marked as returned.");
    }
}
