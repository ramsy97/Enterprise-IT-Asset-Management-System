<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Assigned => 'Assigned',
            self::Maintenance => 'Maintenance',
            self::Retired => 'Retired',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Available => 'badge badge-blue',
            self::Assigned => 'badge badge-green',
            self::Maintenance => 'badge badge-amber',
            self::Retired => 'badge badge-red',
        };
    }
}
