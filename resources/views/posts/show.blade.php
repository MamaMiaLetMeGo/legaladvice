@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($post->featured_image)
            <div class="w-full h-96 relative">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-8">
            <!-- Breadcrumb -->
            @if($post->breadcrumb)
                <div class="text-sm text-gray-500 mb-2">{{ $post->breadcrumb }}</div>
            @endif

            <!-- Title and Meta -->
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            <div class="flex items-center text-gray-600 mb-8">
                <span>By {{ $post->author }}</span>
                @if($post->published_date)
                    <span class="mx-2">•</span>
                    <span>{{ \Carbon\Carbon::parse($post->published_date)->format('F j, Y') }}</span>
                @endif
            </div>

            <!-- Categories -->
            @if($post->categories)
                <div class="mb-6">
                    @foreach(explode(',', $post->categories) as $category)
                        <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-600 mr-2">
                            {{ trim($category) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Video -->
            @if($post->video_url)
                <div class="mb-8">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="{{ $post->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                    </div>
                </div>
            @endif

            <!-- Content -->
            <div class="prose max-w-none">
                {!! $post->body_content !!}
            </div>

            <!-- Back Button -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Posts
                </a>
            </div>
        </div>
    </article>
</div>
@endsection