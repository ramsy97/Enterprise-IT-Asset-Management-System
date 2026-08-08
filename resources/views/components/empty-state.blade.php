@props(['title' => 'No records found', 'message' => 'There is nothing to show here yet.', 'action' => null])

<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <span class="material-symbols-outlined text-[40px] text-on-surface-variant/30">inbox</span>
    <h3 class="mt-4 text-headline-md text-on-surface">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-body-md text-on-surface-variant">{{ $message }}</p>
    @if ($action)
        <div class="mt-5">{{ $action }}</div>
    @endif
</div>
