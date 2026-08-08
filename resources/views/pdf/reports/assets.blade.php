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
    <h1>Asset Report</h1>
    <p class="sub">Generated {{ now()->format('d M Y H:i') }} · {{ count($assets) }} asset(s)</p>

    <table>
        <thead>
            <tr>
                <th>Asset Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Location</th>
                <th class="right">Cost</th>
                <th>Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td class="code">{{ $asset->asset_code }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->category?->name ?? '—' }}</td>
                    <td>{{ $asset->status->label() }}</td>
                    <td>{{ $asset->location?->name ?? '—' }}</td>
                    <td class="right">{{ rupiah($asset->purchase_price) }}</td>
                    <td>{{ $asset->currentAssignment?->assignee?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No assets found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
