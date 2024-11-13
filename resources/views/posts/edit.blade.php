@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Post</h1>
        <p class="mt-2 text-sm text-gray-600">Update your post details below.</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <form 
            action="{{ route('admin.posts.update', $post) }}" 
            method="POST" 
            enctype="multipart/form-data"
            class="p-6 space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $post->title) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">
                    Slug
                    <span class="text-gray-400">(URL-friendly version of title)</span>
                </label>
                <input 
                    type="text" 
                    id="slug" 
                    name="slug" 
                    value="{{ old('slug', $post->slug) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required
                >
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categories --}}
            <div>
                <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                <select 
                    id="categories" 
                    name="categories[]" 
                    multiple 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    @foreach($categories as $category)
                        <option 
                            value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="body_content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea 
                    id="body_content" 
                    name="body_content" 
                    rows="10"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required
                >{{ old('body_content', $post->body_content) }}</textarea>
                @error('body_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Featured Image --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Featured Image</label>
                @if($post->featured_image)
                    <div class="mt-2 flex items-center space-x-4">
                        <img 
                            src="{{ $post->featured_image_url }}" 
                            alt="Current featured image" 
                            class="h-32 w-32 object-cover rounded-lg"
                        >
                        <button 
                            type="button"
                            onclick="document.getElementById('remove_image').value = '1'; this.parentElement.remove();"
                            class="text-red-600 hover:text-red-800"
                        >
                            Remove Image
                        </button>
                    </div>
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                @endif
                <div class="mt-2">
                    <input 
                        type="file" 
                        id="featured_image" 
                        name="featured_image"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                </div>
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Breadcrumb --}}
            <div>
                <label for="breadcrumb" class="block text-sm font-medium text-gray-700">Breadcrumb</label>
                <input 
                    type="text" 
                    id="breadcrumb" 
                    name="breadcrumb" 
                    value="{{ old('breadcrumb', $post->breadcrumb) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                @error('breadcrumb')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Video URL --}}
            <div>
                <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                <input 
                    type="url" 
                    id="video_url" 
                    name="video_url" 
                    value="{{ old('video_url', $post->video_url) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                @error('video_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select 
                    id="status" 
                    name="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ $post->status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Published Date --}}
            <div>
                <label for="published_date" class="block text-sm font-medium text-gray-700">Published Date</label>
                <input 
                    type="datetime-local" 
                    id="published_date" 
                    name="published_date" 
                    value="{{ old('published_date', $post->published_date?->format('Y-m-d\TH:i')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                @error('published_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a 
                    href="{{ route('admin.posts.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Update Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    // Initialize Tom Select for categories
    new TomSelect('#categories', {
        plugins: ['remove_button'],
        create: false,
        maxItems: null
    });

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endpush