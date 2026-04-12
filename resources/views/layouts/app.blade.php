<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RUPS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar di kiri --}}
        @include('layouts.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            {{-- SATU Navbar Utama di sini --}}
            @include('layouts.navigation')

            {{-- Header Dinamis (Judul Halaman) --}}
            @isset($header)
                <header class="bg-white border-b border-slate-200">
                    <div class="max-w-7xl mx-auto py-4 px-6 sm:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
     @stack('scripts')
</body>

</html>
