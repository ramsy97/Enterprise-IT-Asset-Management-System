<x-mail::message>
# Warranty Expiring Soon

Hello,

The warranty for asset **{{ $asset->asset_name }}** (**{{ $asset->asset_code }}**) will expire in **{{ $daysLeft }} days**.

| | |
|---|---|
| **Asset Code** | `{{ $asset->asset_code }}` |
| **Brand / Model** | {{ $asset->brand }} {{ $asset->model }} |
| **Serial Number** | `{{ $asset->serial_number }}` |
| **Expiration Date** | {{ $asset->warranty_expires_at?->format('d M Y') }} |

<x-mail::button :url="route('assets.show', $asset)">
View Asset
</x-mail::button>

Please take the necessary action before the warranty period ends.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
