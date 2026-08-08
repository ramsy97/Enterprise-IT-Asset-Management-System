<?php

namespace App\Services;

use App\Enums\AuditStatus;
use App\Models\AuditRecord;
use App\Support\ActivityLogger;
use Illuminate\Support\Str;

class AuditService
{
    public function create(array $data, ?object $evidence = null): AuditRecord
    {
        $data['audit_batch_id'] ??= 'AUD-'.now()->format('Ymd-His');

        if ($evidence) {
            $data['evidence_path'] = $evidence->store('evidence', 'public');
        }

        $audit = AuditRecord::create([
            ...$data,
            'audited_by' => auth()->id(),
        ]);

        ActivityLogger::log('audit', "Audit recorded for {$audit->asset->asset_code}: {$audit->status->value}", [
            'audit_id' => $audit->id,
        ]);

        return $audit;
    }

    public function update(AuditRecord $audit, array $data, ?object $evidence = null): AuditRecord
    {
        if ($evidence) {
            $data['evidence_path'] = $evidence->store('evidence', 'public');
        }

        $audit->update($data);

        ActivityLogger::log('audit', "Audit updated for {$audit->asset->asset_code}");

        return $audit;
    }

    public function delete(AuditRecord $audit): void
    {
        ActivityLogger::log('audit', "Audit deleted for {$audit->asset->asset_code}");
        $audit->delete();
    }

    public function batchId(): string
    {
        return 'AUD-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(3));
    }
}
