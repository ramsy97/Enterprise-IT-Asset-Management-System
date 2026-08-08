<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\AssetLocation;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetLocationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AssetLocation::class);

        $locations = AssetLocation::withCount('assets')->orderBy('name')->get();

        return view('assets.locations', compact('locations'));
    }

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $location = AssetLocation::create($request->validated());

        ActivityLogger::log('location', "Location created: {$location->name}");

        return back()->with('success', "Location {$location->name} created.");
    }

    public function update(UpdateLocationRequest $request, AssetLocation $location): RedirectResponse
    {
        $location->update($request->validated());

        ActivityLogger::log('location', "Location updated: {$location->name}");

        return back()->with('success', "Location {$location->name} updated.");
    }

    public function destroy(AssetLocation $location): RedirectResponse
    {
        if ($location->assets()->exists()) {
            return back()->with('error', 'Cannot delete a location that still has assets.');
        }

        ActivityLogger::log('location', "Location deleted: {$location->name}");
        $location->delete();

        return back()->with('success', 'Location deleted.');
    }
}
