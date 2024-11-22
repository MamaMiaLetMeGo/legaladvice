@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.0.4/dist/trix.css" rel="stylesheet">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Post</h1>
                <p class="mt-2 text-sm text-gray-700">
                    Last updated {{ $post->updated_at->diffForHumans() }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ $post->url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Post
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                {{-- Title --}}
                <div class="sm:col-span-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Title
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            value="{{ old('title', $post->title) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Author Selection --}}
                <div class="sm:col-span-2">
                    <label for="author_id" class="block text-sm font-medium text-gray-700">
                        Author
                    </label>
                    <div class="mt-1">
                        @if(auth()->user()->is_admin)
                            <select 
                                name="author_id" 
                                id="author_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                @foreach($users as $user)
                                    <option 
                                        value="{{ $user->id }}" 
                                        {{ old('author_id', $post->author_id) === $user->id ? 'selected' : '' }}
                                    >
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="text" 
                                value="{{ auth()->user()->name }}" 
                                class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" 
                                disabled
                            >
                            <input type="hidden" name="author_id" value="{{ auth()->id() }}">
                        @endif
                    </div>
                    @error('author_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="sm:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">
                        Slug
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="slug" 
                            id="slug" 
                            value="{{ old('slug', $post->slug) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categories --}}
                <div class="sm:col-span-6">
                    <label for="categories" class="block text-sm font-medium text-gray-700">
                        Categories
                    </label>
                    <div class="mt-1">
                        <select 
                            name="categories[]" 
                            id="categories" 
                            multiple
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                    </div>
                    @error('categories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Featured Image --}}
                <div class="sm:col-span-6">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700">
                        Featured Image
                    </label>
                    <div class="mt-1 flex items-center">
                        @if($post->featured_image)
                            <div class="relative">
                                <img 
                                    src="{{ $post->featured_image_url }}" 
                                    alt="Current featured image" 
                                    class="h-32 w-32 object-cover rounded-lg"
                                >
                                <button 
                                    type="button"
                                    onclick="document.getElementById('remove_image').value = '1'; this.closest('div').remove();"
                                    class="absolute -top-2 -right-2 rounded-full bg-red-100 p-1 text-red-600 hover:bg-red-200"
                                >
                                    <span class="sr-only">Remove Image</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                        <input 
                            type="file" 
                            name="featured_image" 
                            id="featured_image" 
                            accept="image/*"
                            class="ml-5 block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                    </div>
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="sm:col-span-6">
                    <label for="body_content" class="block text-sm font-medium text-gray-700">
                        Content
                    </label>
                    <div class="mt-1">
                        <input id="body_content" type="hidden" name="body_content" value="{{ old('body_content', $post->body_content) }}">
                        <trix-editor 
                            input="body_content"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 prose max-w-none min-h-[20rem]"
                        ></trix-editor>
                    </div>
                    @error('body_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Video URL --}}
                <div class="sm:col-span-6">
                    <label for="video_url" class="block text-sm font-medium text-gray-700">
                        Video URL
                    </label>
                    <div class="mt-1">
                        <input 
                            type="url" 
                            name="video_url" 
                            id="video_url" 
                            value="{{ old('video_url', $post->video_url) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    @error('video_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status and Publish Date --}}
                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Status
                    </label>
                    <div class="mt-1">
                        <select 
                            name="status" 
                            id="status"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="published_date" class="block text-sm font-medium text-gray-700">
                        Publish Date
                    </label>
                    <div class="mt-1">
                        <input 
                            type="datetime-local" 
                            name="published_date" 
                            id="published_date" 
                            value="{{ old('published_date', optional($post->published_date)->format('Y-m-d\TH:i')) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a 
                        href="{{ route('admin.posts.index') }}"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        name="action"
                        value="save"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Save Changes
                    </button>
                    <button
                        type="submit"
                        name="action"
                        value="publish"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        {{ $post->status === 'published' ? 'Update & Republish' : 'Publish' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/trix@2.0.4/dist/trix.umd.min.js"></script>
<script>
    // Initialize Tom Select for categories
    new TomSelect('#categories', {
        plugins: ['remove_button'],
        create: false
    });

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Preview image before upload
    document.getElementById('featured_image').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.createElement('div');
                preview.className = 'relative';
                preview.innerHTML = `
                    <img src="${event.target.result}" class="h-32 w-32 object-cover rounded-lg">
                    <button 
                        type="button"
                        onclick="this.closest('div').remove(); document.getElementById('featured_image').value = '';"
                        class="absolute -top-2 -right-2 rounded-full bg-red-100 p-1 text-red-600 hover:bg-red-200"
                    >
                        <span class="sr-only">Remove Image</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                const container = document.querySelector('#featured_image').parentElement;
                container.insertBefore(preview, container.firstChild);
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endpush