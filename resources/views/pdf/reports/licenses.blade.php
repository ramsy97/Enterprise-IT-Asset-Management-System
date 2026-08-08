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
    <h1>Software License Report</h1>
    <p class="sub">Generated {{ now()->format('d M Y H:i') }} · {{ count($licenses) }} license(s)</p>

    <table>
        <thead>
            <tr>
                <th>Software</th>
                <th>Vendor</th>
                <th class="right">Seats</th>
                <th class="right">Used</th>
                <th class="right">Available</th>
                <th class="right">Usage %</th>
                <th class="right">Cost</th>
                <th>Expires</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($licenses as $license)
                <tr>
                    <td>{{ $license->software_name }}</td>
                    <td>{{ $license->vendor ?? '—' }}</td>
                    <td class="right">{{ $license->total_licenses }}</td>
                    <td class="right">{{ $license->used_licenses }}</td>
                    <td class="right">{{ $license->getAvailableLicenses() }}</td>
                    <td class="right">{{ $license->getUsagePercent() }}%</td>
                    <td class="right">{{ rupiah($license->purchase_cost) }}</td>
                    <td>{{ $license->expires_at?->format('d M Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No licenses found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
