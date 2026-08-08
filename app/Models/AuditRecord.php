<?php

namespace App\Models;

use App\Enums\AuditStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'audited_by',
        'audit_batch_id',
        'audit_date',
        'status',
        'condition',
        'location_match',
        'findings',
        'evidence_path',
    ];

    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
            'location_match' => 'boolean',
            'status' => AuditStatus::class,
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}
