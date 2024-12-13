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
                $assetUrl = rtrim(config('app.url'), '/');
                
                // Debug information
                if (config('app.debug')) {
                    dump([
                        'manifestPath' => $manifestPath,
                        'manifestExists' => file_exists($manifestPath),
                        'manifest' => $manifest
                    ]);
                }
            @endphp
            @if(!empty($manifest))
                @foreach($manifest as $entry)
                    @if(isset($entry['css']))
                        @foreach($entry['css'] as $css)
                            <link rel="stylesheet" href="{{ $assetUrl }}/build/{{ $css }}">
                        @endforeach
                    @endif
                    @if(isset($entry['file']) && str_ends_with($entry['file'], '.js'))
                        <script type="module" src="{{ $assetUrl }}/build/{{ $entry['file'] }}"></script>
                    @endif
                    @if(isset($entry['imports']))
                        @foreach($entry['imports'] as $import)
                            <link rel="modulepreload" href="{{ $assetUrl }}/build/{{ $import }}">
                        @endforeach
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