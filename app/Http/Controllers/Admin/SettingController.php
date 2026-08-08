<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService) {}

    public function index(): View
    {
        $this->authorize('users.manage');

        return view('admin.settings.index', ['settings' => $this->settingService->all()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('users.manage');

        $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        $this->settingService->setMany($request->only([
            'company_name',
            'company_address',
            'company_phone',
            'currency',
        ]));

        return back()->with('success', 'Settings saved.');
    }
}
