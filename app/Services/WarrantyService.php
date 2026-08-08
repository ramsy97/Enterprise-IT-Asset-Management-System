<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Carbon;

class WarrantyService
{
    public function expiringWithinDays(int $days): \Illuminate\Database\Eloquent\Collection
    {
        return Asset::with('category', 'currentHolder')
            ->whereNotNull('warranty_expires_at')
            ->where('status', '!=', 'retired')
            ->whereBetween('warranty_expires_at', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->orderBy('warranty_expires_at')
            ->get();
    }

    public function expired(): \Illuminate\Database\Eloquent\Collection
    {
        return Asset::with('category')
            ->whereNotNull('warranty_expires_at')
            ->where('status', '!=', 'retired')
            ->where('warranty_expires_at', '<', now()->toDateString())
            ->get();
    }

    public function daysLeft(?Carbon $expiry): ?int
    {
        if (! $expiry) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
    }
}
