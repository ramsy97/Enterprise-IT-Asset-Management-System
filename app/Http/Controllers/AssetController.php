<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function __construct(private readonly AssetService $assetService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);

        $assets = Asset::with(['category', 'location', 'currentHolder'])
            ->filter($request->only(['search', 'category_id', 'location_id', 'status']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => AssetLocation::orderBy('name')->get(),
            'filters' => $request->only(['search', 'category_id', 'location_id', 'status']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Asset::class);

        return view('assets.create', [
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => AssetLocation::orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $this->authorize('create', Asset::class);

        $asset = $this->assetService->store($request->validated());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', "Asset {$asset->asset_code} has been registered and its QR code generated.");
    }

    public function show(Asset $asset): View
    {
        $this->authorize('view', $asset);

        $asset->load([
            'category',
            'location',
            'currentHolder',
            'assignments' => fn ($q) => $q->with('employee', 'approver')->latest(),
            'maintenanceRecords' => fn ($q) => $q->with('technician')->latest(),
            'audits' => fn ($q) => $q->with('auditor')->latest(),
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset): View
    {
        $this->authorize('update', $asset);

        return view('assets.edit', [
            'asset' => $asset,
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => AssetLocation::orderBy('name')->get(),
            'users' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $this->authorize('update', $asset);

        $this->assetService->update($asset, $request->validated());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', "Asset {$asset->asset_code} updated successfully.");
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);

        $this->assetService->delete($asset);

        return redirect()
            ->route('assets.index')
            ->with('success', "Asset {$asset->asset_code} deleted.");
    }
}
