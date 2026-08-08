<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', 'DejaVu Sans', sans-serif; color: #1c1b1f; font-size: 10px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .sub { color: #62656a; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 6px 8px; border-bottom: 2px solid #c7cdd6; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border-bottom: 1px solid #e3e5ea; }
        .code { font-family: 'DejaVu Sans Mono', monospace; }
        .right { text-align: right; }
        .muted { color: #62656a; }
    </style>
</head>
<body>
    <h1>Audit Report</h1>
    <p class="sub">Generated {{ now()->format('d M Y H:i') }} · {{ count($audits) }} record(s)</p>

    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Batch</th>
                <th>Audit Date</th>
                <th>Auditor</th>
                <th>Condition</th>
                <th>Location Match</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($audits as $audit)
                <tr>
                    <td class="code">{{ $audit->asset?->asset_code ?? '—' }}</td>
                    <td>{{ $audit->audit_batch_id }}</td>
                    <td>{{ $audit->audit_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $audit->auditor?->name ?? '—' }}</td>
                    <td>{{ $audit->condition ?: '—' }}</td>
                    <td>{{ $audit->location_match ? 'Matched' : 'Mismatch' }}</td>
                    <td>{{ $audit->status->label() }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No audit records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
