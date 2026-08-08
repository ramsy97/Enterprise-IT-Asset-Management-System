@php
    $user = auth()->user();
    $current = request()->route()?->getName();
    $can = fn (string $permission): bool => $user->can($permission);

    $nav = [];

    $nav[] = [
        'label' => 'Dashboard',
        'icon' => 'dashboard',
        'route' => $user->homeRoute(),
        'active' => in_array($current, ['dashboard', 'admin.dashboard', 'staff.dashboard', 'manager.dashboard']),
        'visible' => true,
    ];

    $nav[] = [
        'label' => 'Asset Management',
        'icon' => 'inventory_2',
        'route' => 'assets.index',
        'active' => str_starts_with((string) $current, 'assets.'),
        'visible' => $can('assets.view'),
    ];

    $nav[] = [
        'label' => 'Asset Assignment',
        'icon' => 'assignment_ind',
        'route' => 'assignments.index',
        'active' => str_starts_with((string) $current, 'assignments.'),
        'visible' => $can('assignments.view'),
    ];

    $nav[] = [
        'label' => 'Maintenance',
        'icon' => 'build',
        'route' => 'maintenance.index',
        'active' => str_starts_with((string) $current, 'maintenance.'),
        'visible' => $can('maintenance.view'),
    ];

    $nav[] = [
        'label' => 'Warranty',
        'icon' => 'verified_user',
        'route' => 'warranty.index',
        'active' => str_starts_with((string) $current, 'warranty.'),
        'visible' => $can('warranty.view'),
    ];

    $nav[] = [
        'label' => 'Software License',
        'icon' => 'terminal',
        'route' => 'licenses.index',
        'active' => str_starts_with((string) $current, 'licenses.'),
        'visible' => $can('licenses.view'),
    ];

    $nav[] = [
        'label' => 'Audit Management',
        'icon' => 'fact_check',
        'route' => 'audits.index',
        'active' => str_starts_with((string) $current, 'audits.'),
        'visible' => $can('audits.view'),
    ];

    $nav[] = [
        'label' => 'Reports',
        'icon' => 'analytics',
        'route' => 'reports.index',
        'active' => str_starts_with((string) $current, 'reports.'),
        'visible' => $can('reports.view'),
    ];

    $adminGroup = $can('users.manage');

    $nav[] = [
        'label' => 'Users & Roles',
        'icon' => 'group',
        'route' => 'admin.users.index',
        'active' => in_array($current, ['admin.users.index', 'admin.users.create', 'admin.users.edit', 'admin.roles.index', 'admin.roles.create', 'admin.roles.edit']),
        'visible' => $adminGroup,
    ];

    $nav[] = [
        'label' => 'Settings',
        'icon' => 'settings',
        'route' => 'admin.settings',
        'active' => $current === 'admin.settings',
        'visible' => $adminGroup,
    ];
@endphp

<aside class="fixed inset-y-0 left-0 z-50 hidden w-60 flex-col bg-primary-container md:flex">
    <div class="px-4 pb-6 pt-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-brand-logo class="h-9 w-9 rounded-lg" />
            <div>
                <h1 class="text-headline-md font-bold leading-6 text-white">ITAMS</h1>
                <p class="text-label-md text-on-primary-container">Enterprise</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-2 pb-4">
        @foreach ($nav as $item)
            @if ($item['visible'])
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg border-l-2 px-3.5 py-2.5 transition-colors duration-150 {{ $item['active'] ? 'border-secondary bg-secondary-container/10 font-semibold text-white' : 'border-transparent text-on-primary-container/70 hover:bg-on-primary-fixed-variant/10 hover:text-on-primary-container' }}">
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
