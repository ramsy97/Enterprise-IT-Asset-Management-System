<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_KEY = 'itams.settings';

    private const DEFAULTS = [
        'company_name' => 'ITAMS Enterprise',
        'company_address' => '',
        'company_phone' => '',
        'currency' => 'IDR',
    ];

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $rows = Setting::pluck('value', 'key')->toArray();

            return array_merge(self::DEFAULTS, $rows);
        });
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->all()[$key] ?? $default;
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'general']);
        }

        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
