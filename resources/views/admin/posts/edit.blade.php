@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/trix@2.0.4/dist/trix.css" rel="stylesheet">
    <style>
        /* Add the same Trix styles as in create.blade.php */
        trix-editor {
            @apply block w-full rounded-md border-gray-300;
            min-height: 20rem;
        }
        
        trix-toolbar {
            @apply border border-gray-300 rounded-t-md bg-gray-50 p-2;
        }
        
        /* ... rest of your Trix styles ... */
    </style>
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
                        <div class="featured-image-preview {{ $post->featured_image ? '' : 'hidden' }} relative">
                            <img 
                                src="{{ $post->featured_image_url }}" 
                                alt="Featured image preview" 
                                class="h-32 w-32 object-cover rounded-lg"
                            >
                            <button 
                                type="button"
                                onclick="removeImage()"
                                class="absolute -top-2 -right-2 rounded-full bg-red-100 p-1 text-red-600 hover:bg-red-200"
                            >
                                <span class="sr-only">Remove Image</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                        <input 
                            type="file" 
                            name="featured_image" 
                            id="featured_image" 
                            accept="image/*"
                            class="hidden"
                        >
                        <button 
                            type="button" 
                            onclick="document.getElementById('featured_image').click()"
                            class="ml-5 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Select Image
                        </button>
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
                <div class="mb-6">
                    <label for="video" class="block text-sm font-medium text-gray-700">Video</label>
                    <div class="mt-1 flex items-center">
                        <div class="video-preview {{ $post->video ? '' : 'hidden' }} relative">
                            <video 
                                width="320" 
                                height="240" 
                                controls 
                                class="rounded-lg"
                            >
                                <source src="{{ $post->video ? Storage::url($post->video) : '' }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <button 
                                type="button"
                                onclick="removeVideo()"
                                class="absolute -top-2 -right-2 rounded-full bg-red-100 p-1 text-red-600 hover:bg-red-200"
                            >
                                <span class="sr-only">Remove Video</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <input 
                            type="file" 
                            name="video" 
                            id="video" 
                            accept="video/mp4,video/quicktime"
                            class="hidden"
                        >
                        <button 
                            type="button" 
                            onclick="document.getElementById('video').click()"
                            class="ml-5 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            {{ $post->video ? 'Change Video' : 'Select Video' }}
                        </button>
                    </div>
                    @error('video')
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
    // Move removeImage function outside to make it globally accessible
    function removeImage() {
        const fileInput = document.getElementById('featured_image');
        const preview = document.querySelector('.featured-image-preview');
        const removeImageInput = document.getElementById('remove_image');
        
        fileInput.value = '';
        preview.classList.add('hidden');
        preview.querySelector('img').src = '';
        removeImageInput.value = '1'; // Set remove_image flag
    }

    function removeVideo() {
        const fileInput = document.getElementById('video');
        const preview = document.querySelector('.video-preview');
        const video = preview.querySelector('video source');
        
        fileInput.value = '';
        preview.classList.add('hidden');
        video.src = '';
        video.parentElement.load();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Add this before other initializations
        window.addEventListener('trix-initialize', function(event) {
            const toolbar = event.target.toolbarElement;
            const buttonGroups = toolbar.querySelector(".trix-button-groups");
            
            // Add custom buttons
            const customButtons = `
                <div class="trix-button-group">
                    <button type="button" class="trix-button" data-trix-attribute="heading1" title="Heading 1">H1</button>
                    <button type="button" class="trix-button" data-trix-attribute="heading2" title="Heading 2">H2</button>
                    <button type="button" class="trix-button" data-trix-attribute="heading3" title="Heading 3">H3</button>
                </div>
                <div class="trix-button-group">
                    <button type="button" class="trix-button" data-trix-attribute="code" title="Code">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </button>
                    <button type="button" class="trix-button" data-trix-attribute="highlight" title="Highlight">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            buttonGroups.insertAdjacentHTML('beforeend', customButtons);

            // Configure custom attributes
            const editor = event.target.editor;
            editor.composition.addAttributeForTag("h1", "heading1");
            editor.composition.addAttributeForTag("h2", "heading2");
            editor.composition.addAttributeForTag("h3", "heading3");
            editor.composition.addAttributeForTag("span", "highlight");
        });

        // Rest of your existing initialization code...
        new TomSelect('#categories', {
            plugins: ['remove_button'],
            create: false
        });
        
        // Preview image before upload
        document.getElementById('featured_image').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                const preview = document.querySelector('.featured-image-preview');
                const previewImage = preview.querySelector('img');
                const removeImageInput = document.getElementById('remove_image');
                
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    preview.classList.remove('hidden');
                    removeImageInput.value = '0'; // Reset remove_image flag when new image is selected
                };
                
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        document.getElementById('video').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                const url = URL.createObjectURL(file);
                const preview = document.querySelector('.video-preview');
                const video = preview.querySelector('video source');
                
                video.src = url;
                video.parentElement.load(); // Reload video element
                preview.classList.remove('hidden');
            }
        });
    });
</script>
@endpush