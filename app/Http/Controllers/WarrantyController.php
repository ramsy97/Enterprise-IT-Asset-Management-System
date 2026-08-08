<?php

namespace App\Http\Controllers;

use App\Services\WarrantyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function __construct(private readonly WarrantyService $warrantyService) {}

    public function index(Request $request): View
    {
        $days = (int) $request->get('days', 90);

        $expiring = $this->warrantyService->expiringWithinDays($days);
        $expired = $this->warrantyService->expired();

        return view('warranty.index', [
            'expiring' => $expiring,
            'expired' => $expired,
            'days' => $days,
        ]);
    }
}
