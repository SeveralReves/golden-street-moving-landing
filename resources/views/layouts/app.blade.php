<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="admin" x-data="{ menu: false }" @keydown.escape.window="menu = false">
            @include('layouts.navigation')

            <div class="admin__backdrop" x-show="menu" x-cloak x-transition.opacity @click="menu = false"></div>

            <div class="admin__main">
                <header class="admin__topbar">
                    <button type="button" class="admin__burger" @click="menu = true" aria-label="Open menu">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="admin__heading">
                        @if (isset($header))
                            <h1 class="admin__title">{{ $header }}</h1>
                        @endif
                        @if (isset($subheader))
                            <p class="admin__subtitle">{{ $subheader }}</p>
                        @endif
                    </div>
                </header>

                <main class="admin__content">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
