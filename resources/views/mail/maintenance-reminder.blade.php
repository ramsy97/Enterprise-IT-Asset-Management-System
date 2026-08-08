<x-mail::message>
# Maintenance Reminder

Hello,

A **{{ $record->type->label() }}** is scheduled for asset **{{ $record->asset?->asset_name }}** (**{{ $record->asset?->asset_code }}**).

| | |
|---|---|
| **Asset Code** | `{{ $record->asset?->asset_code }}` |
| **Scheduled Date** | {{ $record->scheduled_date?->format('d M Y') }} |
| **Type** | {{ $record->type->label() }} |
| **Description** | {{ $record->description }} |

<x-mail::button :url="route('maintenance.show', $record)">
View Maintenance
</x-mail::button>

Make sure the asset is ready and the technician is informed.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
