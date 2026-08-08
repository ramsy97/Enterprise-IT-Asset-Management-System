<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center"
             style="background-image: url('{{ asset('img/login-bg.jpg') }}');">
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px]"></div>

            <div class="relative z-10 flex flex-col items-center">
                <div>
                    <a href="/" class="flex flex-col items-center gap-3">
                        <x-brand-logo class="h-20 w-20 drop-shadow-md" />
                        <div class="text-center">
                            <span class="block text-xl font-bold tracking-tight text-white drop-shadow">{{ config('app.name', 'ITAMS') }}</span>
                            <span class="block text-[11px] font-medium uppercase tracking-widest text-slate-300">Enterprise IT Asset Management</span>
                        </div>
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-xl overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
