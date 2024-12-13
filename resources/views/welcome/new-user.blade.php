@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Notifications Section --}}
    @if (session('success'))
        <div class="mb-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('unsubscribed'))
        <div class="mb-8 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">
                {{ session('unsubscribed') }}
                @if(session('category'))
                    You've unsubscribed from {{ session('category') }} updates.
                @else
                    You've unsubscribed from all updates.
                @endif
            </span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome aboard, {{ auth()->user()->name }}! 🎉</h1>
            
            <div class="prose max-w-none">
                <p class="text-lg text-gray-600 mb-8">
                    Thank you for joining our legal community! We are excited to have you here.
                </p>

                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    {{-- Newsletter Section --}}
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-green-900 mb-4">
                            📧 Stay Updated
                        </h2>
                        <p class="text-green-800 mb-4">
                            Choose which updates you'd like to receive:
                        </p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-2">
                                @foreach($categories as $category)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" 
                                               name="category_updates[{{ $category->id }}]" 
                                               class="rounded" 
                                               {{ auth()->user()->newsletterSubscription?->categories->contains($category->id) ? 'checked' : '' }}>
                                        <span>{{ $category->name }} Updates</span>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" 
                                    class="inline-block px-6 py-2 rounded-md transition
                                           {{ auth()->user()->newsletterSubscription ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} 
                                           text-white">
                                {{ auth()->user()->newsletterSubscription ? 'Update Preferences' : 'Subscribe to Updates' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" 
                       class="inline-block bg-gray-600 text-white px-8 py-3 rounded-md hover:bg-gray-700 transition">
                        Start Exploring
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 