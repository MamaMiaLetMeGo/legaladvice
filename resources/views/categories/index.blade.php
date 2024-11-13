@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Categories</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($categories as $category)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <a href="{{ route('categories.show', $category->slug) }}" class="block">
                    @if($category->image)
                        <img src="{{ asset($category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-48 object-cover rounded-md mb-4">
                    @endif
                    
                    <h2 class="text-xl font-semibold mb-2">{{ $category->name }}</h2>
                    
                    @if($category->description)
                        <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                    @endif

                    <div class="text-sm text-gray-500">
                        {{ $category->posts_count ?? 0 }} posts
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No categories found.</p>
            </div>
        @endforelse
    </div>

    @if($categories->hasPages())
        <div class="mt-8">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
