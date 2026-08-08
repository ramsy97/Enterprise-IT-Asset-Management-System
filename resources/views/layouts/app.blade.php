<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>{{ $title ?? config('app.name', 'ITAMS Enterprise') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="min-h-screen bg-background">
        <div class="flex min-h-screen">
            @include('layouts.sidebar')
            @include('layouts.sidebar-mobile')

            <div class="flex min-w-0 flex-1 flex-col md:pl-60">
                @include('layouts.topbar')

                <main class="flex-1 px-6 py-8 md:px-8">
                    @if (session('success'))
                        <div class="mb-6 flex items-center gap-3 rounded-xl border border-[#10b981]/30 bg-[#10b981]/10 px-4 py-3 text-sm font-medium text-[#067a4f]" x-data="{ show: true }" x-show="show">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="ml-auto text-[#067a4f]/70 hover:text-[#067a4f]" @click="show = false">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-center gap-3 rounded-xl border border-error/30 bg-error/10 px-4 py-3 text-sm font-medium text-on-error-container" x-data="{ show: true }" x-show="show">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="ml-auto opacity-70 hover:opacity-100" @click="show = false">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                    @endif

                    {{ $slot ?? '' }}
                    @yield('content')
                </main>

                @include('layouts.footer')
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
