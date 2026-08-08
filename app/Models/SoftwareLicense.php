<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'software_name',
        'vendor',
        'license_key',
        'total_licenses',
        'used_licenses',
        'purchase_date',
        'purchase_cost',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'expires_at' => 'date',
            'purchase_cost' => 'decimal:2',
            'total_licenses' => 'integer',
            'used_licenses' => 'integer',
        ];
    }

    public function getAvailableLicenses(): int
    {
        return max(0, $this->total_licenses - $this->used_licenses);
    }

    public function getUsagePercent(): int
    {
        if ($this->total_licenses === 0) {
            return 0;
        }

        return (int) round($this->used_licenses / $this->total_licenses * 100);
    }
}
