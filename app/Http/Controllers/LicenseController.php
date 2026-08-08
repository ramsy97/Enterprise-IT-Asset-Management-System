<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLicenseRequest;
use App\Http\Requests\UpdateLicenseRequest;
use App\Models\SoftwareLicense;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', SoftwareLicense::class);

        $licenses = SoftwareLicense::orderBy('software_name')->paginate(15);

        return view('licenses.index', compact('licenses'));
    }

    public function create(): View
    {
        $this->authorize('create', SoftwareLicense::class);

        return view('licenses.create');
    }

    public function store(StoreLicenseRequest $request): RedirectResponse
    {
        $this->authorize('create', SoftwareLicense::class);

        $data = $request->validated();
        if (! empty($data['license_key'])) {
            $data['license_key'] = Crypt::encryptString($data['license_key']);
        }

        $license = SoftwareLicense::create($data);

        ActivityLogger::log('license', "License added: {$license->software_name}");

        return redirect()->route('licenses.index')->with('success', "License {$license->software_name} added.");
    }

    public function edit(SoftwareLicense $license): View
    {
        $this->authorize('update', $license);

        return view('licenses.edit', compact('license'));
    }

    public function update(UpdateLicenseRequest $request, SoftwareLicense $license): RedirectResponse
    {
        $this->authorize('update', $license);

        $data = $request->validated();
        if (! empty($data['license_key'])) {
            $data['license_key'] = Crypt::encryptString($data['license_key']);
        } else {
            unset($data['license_key']);
        }

        $license->update($data);

        ActivityLogger::log('license', "License updated: {$license->software_name}");

        return redirect()->route('licenses.index')->with('success', "License {$license->software_name} updated.");
    }

    public function destroy(SoftwareLicense $license): RedirectResponse
    {
        $this->authorize('delete', $license);

        ActivityLogger::log('license', "License deleted: {$license->software_name}");
        $license->delete();

        return redirect()->route('licenses.index')->with('success', 'License deleted.');
    }
}
