@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">All Posts</h1>
        @auth
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Post
            </a>
        @endauth
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                @if($post->featured_image)
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
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
                    <div class="flex justify-between items-center">
                        <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Read More →
                        </a>
                        @auth
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-gray-600 hover:text-gray-900">
                                    Edit
                                </a>
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this post?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No posts available yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection