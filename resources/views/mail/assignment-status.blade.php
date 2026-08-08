<x-mail::message>
# Asset Assignment {{ ucfirst($action) }}

Hello {{ $assignment->employee?->name }},

Your asset assignment request has been **{{ ucfirst($action) }}**.

| | |
|---|---|
| **Asset** | {{ $assignment->asset?->asset_name }} |
| **Asset Code** | `{{ $assignment->asset?->asset_code }}` |
| **Status** | {{ ucfirst($action) }} |
@if($assignment->assigned_date)
| **Assigned Date** | {{ $assignment->assigned_date?->format('d M Y') }} |
@endif

@if($assignment->notes)
**Note:** {{ $assignment->notes }}
@endif

<x-mail::button :url="route('assignments.index')">
View Assignments
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
