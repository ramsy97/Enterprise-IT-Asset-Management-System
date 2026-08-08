@props(['title' => null, 'subtitle' => null, 'actions' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title)
        <div class="flex items-center justify-between border-b border-outline-variant/70 px-5 py-4">
            <div>
                <h3 class="text-headline-md text-on-surface">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="mt-0.5 text-body-sm text-on-surface-variant">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif

    <div @class(['px-5 py-5' => $padding])>
        {{ $slot }}
    </div>
</div>
