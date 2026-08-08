<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case Preventive = 'preventive';
    case Repair = 'repair';
    case Replacement = 'replacement';

    public function label(): string
    {
        return match ($this) {
            self::Preventive => 'Preventive Maintenance',
            self::Repair => 'Repair',
            self::Replacement => 'Replacement',
        };
    }
}
