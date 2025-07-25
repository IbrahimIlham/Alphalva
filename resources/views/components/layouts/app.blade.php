<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Alphalva' }}</title>

        @livewireStyles
        @vite(['resources/js/app.js', 'resources/css/app.css'])
    </head>
    <body class="bg-slate-200 dark:bg-slate-700">
        <main>
            @livewire('partials.navbar')
            {{ $slot }}
            @livewire('partials.footer')
        </main>
        @livewireScripts
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
        <script src="https://unpkg.com/preline@latest/dist/preline.js"></script>
        <script>
            function initDropdown() {
                if (window.HS && HS.Dropdown) {
                    HS.Dropdown.autoInit();
                }
            }
            document.addEventListener("DOMContentLoaded", initDropdown);
            document.addEventListener("livewire:navigated", initDropdown);
            document.addEventListener("livewire:load", initDropdown);
            document.addEventListener("livewire:update", initDropdown);
        </script>
    </body>
</html>
