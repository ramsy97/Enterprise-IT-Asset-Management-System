<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    private const CACHE_TTL = 604800; // 7 days
    private const DISK = 'public';
    private const DIR = 'qrcodes';

    public function generate(Asset $asset): string
    {
        $cacheKey = "qr:{$asset->asset_code}";

        $relative = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($asset) {
            $url = route('qr.show', $asset->asset_code, absolute: true);
            $filename = self::DIR.'/'.$asset->asset_code.'.svg';

            $image = QrCode::format('svg')
                ->size(360)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($url);

            Storage::disk(self::DISK)->put($filename, $image);

            return $filename;
        });

        return $relative;
    }

    public function renderSvg(Asset $asset): string
    {
        $url = route('qr.show', $asset->asset_code, absolute: true);

        return QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($url);
    }

    public function flush(Asset $asset): void
    {
        Cache::forget("qr:{$asset->asset_code}");
        Storage::disk(self::DISK)->delete(self::DIR.'/'.$asset->asset_code.'.svg');
    }

    public function assetRoute(Asset $asset): string
    {
        return route('qr.show', $asset->asset_code, absolute: true);
    }
}
