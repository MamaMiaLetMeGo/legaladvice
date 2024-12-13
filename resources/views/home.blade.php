@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <!-- Hero Section with Video Background -->
    <div class="relative overflow-hidden bg-white min-h-[506px]">
        <!-- Video Background -->
        <div class="absolute inset-0">
            <video 
                autoplay 
                loop 
                muted 
                playsinline 
                class="w-full h-full object-cover"
                style="min-height: 506px"
            >
                <source src="{{ asset('videos/hero-bg.mp4') }}" type="video/mp4">
            </video>
            <!-- Simple Black Overlay -->
            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <!-- Chat Box -->
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-md">
                    <!-- Chat Header -->
                    <div class="bg-blue-600 p-4">
                        <h2 class="text-white text-lg font-semibold">Chat with a Legal Expert</h2>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div class="bg-gray-50 h-80 p-4 overflow-y-auto">
                        <div class="space-y-4">
                            <!-- Initial welcome message (animated) -->
                            <div class="opacity-0 animate-fade-in">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 bg-white rounded-lg py-2 px-4 shadow-sm">
                                        <p class="text-gray-800">Hello! What legal matter can I help you with today?</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Input Area -->
                    <div class="p-4 bg-white border-t">
                        <div class="flex space-x-3">
                            <input 
                                type="text" 
                                placeholder="Type your message..." 
                                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                            >
                            <button class="bg-blue-600 text-white rounded-lg px-4 py-2 font-semibold hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right side content -->
                <div class="text-center md:text-left text-white">
                    <h1 class="text-4xl font-bold mb-4">Get Legal Help Now</h1>
                    <p class="text-xl opacity-90">Connect instantly with qualified legal experts ready to assist you with your questions.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured/Latest Post -->
    @if($posts->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="grid md:grid-cols-2 items-center">
                    @if($posts->first()->featured_image)
                        <div class="h-72 md:h-96">
                            <img src="{{ $posts->first()->featured_image_url }}" 
                                 alt="{{ $posts->first()->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-8 md:p-12">
                        <div class="uppercase tracking-wide text-sm text-blue-600 font-semibold">Latest Post</div>
                        <a href="{{ $posts->first()->url }}" class="block mt-2">
                            <h2 class="text-2xl font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                {{ $posts->first()->title }}
                            </h2>
                        </a>
                        <p class="mt-4 text-gray-500 line-clamp-3">{{ $posts->first()->excerpt }}</p>
                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ $posts->first()->author->profile_image_url }}" 
                                     alt="{{ $posts->first()->author->name }}" 
                                     class="h-10 w-10 rounded-full">
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $posts->first()->author->name }}</p>
                                <div class="flex space-x-4 text-sm text-gray-500">
                                    <span>{{ $posts->first()->published_date->format('M d, Y') }}</span>
                                    <span>{{ $posts->first()->reading_time }} min read</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Posts Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Recent Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts->skip(1) as $post)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    @if($post->featured_image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    <div class="p-6">
                        @if($post->categories->isNotEmpty())
                            <div class="text-xs text-blue-600 font-semibold tracking-wide uppercase mb-2">
                                {{ $post->categories->first()->name }}
                            </div>
                        @endif
                        <a href="{{ $post->url }}" class="block">
                            <h3 class="text-xl font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                {{ $post->title }}
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ $post->author->profile_image_url }}" 
                                     alt="{{ $post->author->name }}" 
                                     class="h-8 w-8 rounded-full">
                                <span class="ml-2 text-sm text-gray-600">{{ $post->author->name }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $post->reading_time }} min read</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 1s ease-out forwards;
        animation-delay: 1s;
    }
</style>
@endpush
@endsection
