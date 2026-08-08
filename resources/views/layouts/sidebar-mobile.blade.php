@php
    $user = auth()->user();
    $current = request()->route()?->getName();
    $can = fn (string $permission): bool => $user->can($permission);

    $nav = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $user->homeRoute(), 'visible' => true],
        ['label' => 'Asset Management', 'icon' => 'inventory_2', 'route' => 'assets.index', 'visible' => $can('assets.view')],
        ['label' => 'Asset Assignment', 'icon' => 'assignment_ind', 'route' => 'assignments.index', 'visible' => $can('assignments.view')],
        ['label' => 'Maintenance', 'icon' => 'build', 'route' => 'maintenance.index', 'visible' => $can('maintenance.view')],
        ['label' => 'Warranty', 'icon' => 'verified_user', 'route' => 'warranty.index', 'visible' => $can('warranty.view')],
        ['label' => 'Software License', 'icon' => 'terminal', 'route' => 'licenses.index', 'visible' => $can('licenses.view')],
        ['label' => 'Audit Management', 'icon' => 'fact_check', 'route' => 'audits.index', 'visible' => $can('audits.view')],
        ['label' => 'Reports', 'icon' => 'analytics', 'route' => 'reports.index', 'visible' => $can('reports.view')],
        ['label' => 'Users & Roles', 'icon' => 'group', 'route' => 'admin.users.index', 'visible' => $can('users.manage')],
        ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings', 'visible' => $can('users.manage')],
    ];
@endphp

<div class="fixed inset-0 z-[60] hidden" x-data="{ open: false }" x-init="window.addEventListener('open-sidebar', () => { open = true; document.body.classList.add('overflow-hidden'); })"
     x-on:keydown.escape.window="open = false; document.body.classList.remove('overflow-hidden')"
     x-cloak>
    <div class="fixed inset-0 bg-black/40 transition-opacity" x-show="open" x-transition.opacity @click="open = false; document.body.classList.remove('overflow-hidden')"></div>

    <aside class="fixed inset-y-0 left-0 z-10 flex w-64 flex-col bg-primary-container transition-transform duration-200"
           x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           @click.outside="open = false; document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-between px-4 pb-6 pt-6">
            <div class="flex items-center gap-3">
                <x-brand-logo class="h-9 w-9 rounded-lg" />
                <div>
                    <h1 class="text-headline-md font-bold leading-6 text-white">ITAMS</h1>
                    <p class="text-label-md text-on-primary-container">Enterprise</p>
                </div>
            </div>
            <button type="button" class="text-on-primary-container hover:text-white" @click="open = false; document.body.classList.remove('overflow-hidden')">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-2 pb-4">
            @foreach ($nav as $item)
                @if ($item['visible'])
                    <a href="{{ route($item['route']) }}" @click="open = false; document.body.classList.remove('overflow-hidden')"
                       class="flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-on-primary-container/70 transition-colors hover:bg-on-primary-fixed-variant/10 hover:text-on-primary-container">
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'wght' 300;">{{ $item['icon'] }}</span>
                        <span class="text-body-sm">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-white/10 px-4 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary-fixed-dim text-sm font-bold text-primary-container">{{ $user->initials() }}</span>
                <div class="min-w-0">
                    <p class="truncate text-body-md font-semibold text-white">{{ $user->name }}</p>
                    <p class="truncate text-label-md text-on-primary-container">{{ $user->roleName() }}</p>
                </div>
            </div>
        </div>
    </aside>
</div>
