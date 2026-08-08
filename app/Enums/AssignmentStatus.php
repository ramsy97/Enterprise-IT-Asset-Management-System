<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Returned => 'Returned',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'badge badge-amber',
            self::Approved => 'badge badge-green',
            self::Rejected => 'badge badge-red',
            self::Returned => 'badge badge-gray',
        };
    }
}
