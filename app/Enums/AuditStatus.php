<?php

namespace App\Enums;

enum AuditStatus: string
{
    case Verified = 'verified';
    case NeedRepair = 'need_repair';
    case Missing = 'missing';

    public function label(): string
    {
        return match ($this) {
            self::Verified => 'Verified',
            self::NeedRepair => 'Need Repair',
            self::Missing => 'Missing',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Verified => 'badge badge-green',
            self::NeedRepair => 'badge badge-amber',
            self::Missing => 'badge badge-red',
        };
    }
}
