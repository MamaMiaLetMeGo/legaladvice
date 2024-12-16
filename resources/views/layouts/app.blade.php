<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LegalAdvice.ai') }}</title>

        <!-- Add Pusher configuration before any JavaScript loads -->
        <script>
            window.userId = {{ auth()->check() ? auth()->id() : 'null' }};
            window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
            window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
            window.csrfToken = '{{ csrf_token() }}';
            window.isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        </script>

        @production
            @php
                $manifestPath = public_path('build/.vite/manifest.json');
                $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
                $assetUrl = rtrim(config('app.url'), '/');
            @endphp
            @if(!empty($manifest))
                {{-- CSS Entry --}}
                @if(isset($manifest['resources/css/app.css']))
                    <link rel="stylesheet" href="{{ $assetUrl }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
                @endif

                {{-- JS Entry --}}
                @if(isset($manifest['resources/js/app.js']))
                    <script type="module" src="{{ $assetUrl }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
                @endif
            @else
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @endif
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endproduction

        @stack('styles')

        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
            @include('layouts.footer')
        </div>

        @stack('scripts')
    </body>
</html>