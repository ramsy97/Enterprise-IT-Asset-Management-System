@props(['label', 'value', 'icon', 'trend' => null, 'trendIcon' => null, 'trendText' => null, 'trendColor' => 'text-[#10b981]'])

<div class="card flex flex-col justify-between p-5">
    <div>
        <div class="flex items-center justify-between">
            <h3 class="text-label-md uppercase tracking-wider text-on-surface-variant">{{ $label }}</h3>
            <span class="material-symbols-outlined text-[20px] text-on-surface-variant/70">{{ $icon }}</span>
        </div>
        <div class="mt-2 text-headline-lg text-on-surface">{{ $value }}</div>
    </div>
    @if ($trend !== null)
        <div class="mt-4 flex items-center">
            <span @class(['flex items-center gap-1 rounded px-2 py-1 text-label-md', $trendColor, $trend >= 0 ? 'bg-[#10b981]/10' : 'bg-error/10'])>
                <span class="material-symbols-outlined text-[14px]">{{ $trendIcon ?? ($trend >= 0 ? 'arrow_upward' : 'arrow_downward') }}</span>
                {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
            </span>
            <span class="ml-2 text-body-sm text-on-surface-variant">{{ $trendText ?? 'from last month' }}</span>
        </div>
    @endif
</div>
