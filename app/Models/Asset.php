<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'asset_name',
        'asset_category_id',
        'asset_location_id',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'status',
        'warranty_expires_at',
        'current_holder_id',
        'qr_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expires_at' => 'date',
            'purchase_price' => 'decimal:2',
            'status' => AssetStatus::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AssetLocation::class, 'asset_location_id');
    }

    public function currentHolder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class)
            ->whereIn('status', ['pending', 'approved'])
            ->latestOfMany();
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AuditRecord::class);
    }

    public function warrantyDaysLeft(): ?int
    {
        if (! $this->warranty_expires_at) {
            return null;
        }

        return now()->diffInDays($this->warranty_expires_at, false);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $s) => $q
                ->where(fn ($q) => $q
                    ->where('asset_code', 'like', "%{$s}%")
                    ->orWhere('asset_name', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%")
                    ->orWhere('serial_number', 'like', "%{$s}%"))
                ->orWhereHas('currentHolder', fn ($q) => $q->where('name', 'like', "%{$s}%")))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('asset_category_id', $v))
            ->when($filters['location_id'] ?? null, fn ($q, $v) => $q->where('asset_location_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v));
    }
}
