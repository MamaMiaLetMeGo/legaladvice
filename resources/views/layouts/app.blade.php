<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LegalAdvice.ai') }}</title>

        @production
            @php
                $manifestPath = public_path('build/.vite/manifest.json');
                $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
            @endphp
            @if(!empty($manifest))
                @foreach($manifest as $entry)
                    @if(isset($entry['css']))
                        @foreach($entry['css'] as $css)
                            <link rel="stylesheet" href="{{ asset('build/'.$css) }}">
                        @endforeach
                    @endif
                    @if(str_ends_with($entry['file'], '.js'))
                        <script type="module" src="{{ asset('build/'.$entry['file']) }}"></script>
                    @endif
                @endforeach
            @else
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @endif
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endproduction

        @stack('styles')
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