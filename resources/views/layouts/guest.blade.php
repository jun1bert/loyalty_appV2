<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Martinis & Manicures Loyalty') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/martinis-icon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/martinis-icon.png') }}">
        <meta name="theme-color" content="#D4AF37">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[var(--ink)] antialiased">
        <div class="auth-stage flex min-h-screen items-center justify-center px-4 py-5 sm:py-8">
            <div class="w-full max-w-[420px] sm:max-w-[460px] lg:max-w-[500px]">
                <div class="auth-card px-6 py-7 sm:px-9 sm:py-9 lg:px-12 lg:py-10">
                    {{ $slot }}
                </div>

                <p class="mt-5 text-center text-xs font-medium text-[#E8DDAA] sm:mt-6 sm:text-sm">
                    Martinis &amp; Manicures &copy; {{ now()->year }}
                </p>
            </div>
        </div>
    </body>
</html>

