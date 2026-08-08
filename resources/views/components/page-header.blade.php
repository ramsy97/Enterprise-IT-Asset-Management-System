@props(['title', 'subtitle' => null, 'actions' => null])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h2 class="text-display text-on-surface">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-body-lg text-on-surface-variant">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($actions)
        <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
    @endif
</div>
