<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\QrCodeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QrController extends Controller
{
    public function __construct(private readonly QrCodeService $qrCodeService) {}

    public function show(Asset $asset): View
    {
        $asset->load([
            'category',
            'location',
            'currentHolder',
            'maintenanceRecords' => fn ($q) => $q->latest()->limit(5),
            'assignments' => fn ($q) => $q->latest()->limit(5),
        ]);

        return view('qr.show', compact('asset'));
    }

    public function image(Asset $asset): Response
    {
        $relative = $this->qrCodeService->generate($asset);

        if (! Storage::disk('public')->exists($relative)) {
            $this->qrCodeService->flush($asset);
            $relative = $this->qrCodeService->generate($asset);
        }

        $contents = Storage::disk('public')->get($relative);

        return response($contents, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
