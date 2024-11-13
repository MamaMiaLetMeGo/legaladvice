<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('home') }}">
                                    {{ config('app.name', 'Laravel') }}
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="{{ route('home') }}" class="text-gray-900 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                    Home
                                </a>
                                <a href="{{ route('posts.index') }}" class="text-gray-900 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                                    Posts
                                </a>
                            </div>
                        </div>

                        <!-- Right Side Navigation -->
                        <div class="flex items-center">
                            @auth
                                <div class="relative">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                        <div>{{ Auth::user()->name }}</div>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900">
                                        Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                                <a href="{{ route('register') }}" class="ml-4 text-gray-600 hover:text-gray-900">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
