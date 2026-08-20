<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Phekong') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-dvh flex flex-col items-center justify-center px-4 py-6 sm:py-10">

        {{-- Wordmark --}}
        <div class="mb-8 flex flex-col items-center">
            <div class="w-14 h-14 rounded-2xl bg-phekong flex items-center justify-center shadow-lg shadow-phekong-dark/20">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1.5 3-4 5-4 8.5a4 4 0 108 0C16 8 13.5 6 12 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-6" />
                </svg>
            </div>
            <h1 class="mt-3 text-xl font-bold text-gray-900 tracking-tight">Phekong</h1>
            <p class="text-xs text-gray-400 mt-0.5">Stock &amp; Sales</p>
        </div>

        {{-- Card --}}
        <div class="w-full sm:max-w-md bg-white shadow-lg rounded-2xl px-6 py-8">
            {{ $slot }}
        </div>

    </div>
</body>
</html>