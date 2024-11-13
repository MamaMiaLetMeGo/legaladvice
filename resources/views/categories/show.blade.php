@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Category Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Categories
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            @if($category->image)
                <div class="mb-6">
                    <img src="{{ asset($category->image) }}" 
                         alt="{{ $category->name }}" 
                         class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            <h1 class="text-3xl font-bold mb-4">{{ $category->name }}</h1>
            
            @if($category->description)
                <p class="text-gray-600 mb-4">{{ $category->description }}</p>
            @endif

            <div class="text-sm text-gray-500">
                {{ $posts->total() }} posts in this category
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                    @if($post->featured_image)
                        <img src="{{ asset($post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $post->title }}</h2>
                        
                        @if($post->excerpt)
                            <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt, 100) }}</p>
                        @endif

                        <div class="flex items-center text-sm text-gray-500">
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $post->reading_time ?? '5 min' }} read</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No posts found in this category.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
