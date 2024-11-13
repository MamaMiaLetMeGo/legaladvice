@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                    Welcome to Our Blog
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Discover insightful articles, stories, and updates from our community.
                </p>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="{{ route('admin.posts.create') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                            Create Post
                        </a>
                    </div>
                    <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                        <a href="{{ route('admin.posts.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                            Manage Posts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Posts Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Posts</h2>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @forelse($posts as $post)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    @if($post->featured_image)
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-blue-600">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <div class="text-sm text-gray-500 mb-4">
                            <span>By {{ $post->author }}</span>
                            @if($post->published_date)
                                <span class="mx-2">•</span>
                                <span>{{ \Carbon\Carbon::parse($post->published_date)->format('M d, Y') }}</span>
                            @endif
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ Str::limit(strip_tags($post->body_content), 150) }}
                        </p>
                        @if($post->categories)
                            <div class="mb-4">
                                @foreach(explode(',', $post->categories) as $category)
                                    <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-600 mr-2">
                                        {{ trim($category) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Read More →
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">No posts available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection