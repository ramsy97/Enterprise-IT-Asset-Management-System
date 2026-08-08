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
    <h1>Maintenance Report</h1>
    <p class="sub">Generated {{ now()->format('d M Y H:i') }} · {{ count($records) }} record(s)</p>

    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Type</th>
                <th>Scheduled</th>
                <th>Completed</th>
                <th>Technician</th>
                <th>Status</th>
                <th class="right">Cost</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td class="code">{{ $record->asset?->asset_code ?? '—' }}</td>
                    <td>{{ $record->type->label() }}</td>
                    <td>{{ $record->scheduled_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $record->completed_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $record->technician?->name ?? '—' }}</td>
                    <td>{{ $record->status->label() }}</td>
                    <td class="right">{{ rupiah($record->cost) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No maintenance records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
