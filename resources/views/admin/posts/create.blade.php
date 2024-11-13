@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Post</h1>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('title') }}" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                <input type="text" name="author" id="author" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('author') }}" required>
                @error('author')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" id="slug" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('slug') }}" required>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="breadcrumb" class="block text-sm font-medium text-gray-700">Breadcrumb</label>
                <input type="text" name="breadcrumb" id="breadcrumb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('breadcrumb') }}">
            </div>

            <div class="mb-6">
                <label for="body_content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="body_content" id="body_content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('body_content') }}</textarea>
                @error('body_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image URL</label>
                <input type="text" name="featured_image" id="featured_image" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('featured_image') }}">
            </div>

            <div class="mb-6">
                <label for="categories" class="block text-sm font-medium text-gray-700">
                    Categories
                </label>
                <select 
                    name="categories[]" 
                    id="categories" 
                    multiple
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                <input type="text" name="video_url" id="video_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('video_url') }}">
            </div>

            <div class="mb-6">
                <label for="published_date" class="block text-sm font-medium text-gray-700">Published Date</label>
                <input type="date" name="published_date" id="published_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('published_date') }}">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.posts.index') }}" class="bg-gray-200 py-2 px-4 rounded-md text-gray-700 hover:bg-gray-300">Cancel</a>
                <button type="submit" class="bg-blue-600 py-2 px-4 rounded-md text-white hover:bg-blue-700">Create Post</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    // Initialize Tom Select for categories
    new TomSelect('#categories', {
        plugins: ['remove_button'],
        create: false
    });
</script>
@endpush