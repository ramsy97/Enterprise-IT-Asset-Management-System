<?php

namespace App\Support;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(string $type, string $description, array $properties = []): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'log_type' => $type,
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => request()->ip(),
        ]);
    }
}
