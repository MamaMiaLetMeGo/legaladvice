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
                <div class="mt-1">
                    <input id="body_content" type="hidden" name="body_content" value="{{ old('body_content') }}">
                    <trix-editor 
                        input="body_content"
                        class="trix-content prose max-w-full block w-full border-gray-300 rounded-md shadow-sm"
                        style="min-height: 20rem;">
                    </trix-editor>
                </div>
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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    
    <style>
        trix-editor {
            @apply block w-full rounded-md border-gray-300;
            min-height: 20rem;
        }
        
        trix-toolbar {
            @apply border border-gray-300 rounded-t-md bg-gray-50 p-2;
        }
        
        trix-toolbar .trix-button-group {
            @apply mr-2;
        }
        
        trix-toolbar .trix-button {
            @apply border border-gray-300 rounded p-1 bg-white hover:bg-gray-100;
        }
        
        trix-toolbar .trix-button.trix-active {
            @apply bg-blue-50 border-blue-500;
        }
        
        .trix-content {
            @apply prose max-w-none;
        }
        
        .trix-content ul {
            @apply list-disc pl-4;
        }
        
        .trix-content ol {
            @apply list-decimal pl-4;
        }

        /* New styles for custom buttons */
        trix-toolbar .trix-button[data-trix-attribute="heading1"],
        trix-toolbar .trix-button[data-trix-attribute="heading2"],
        trix-toolbar .trix-button[data-trix-attribute="heading3"] {
            font-family: serif;
            font-weight: bold;
            font-size: 14px;
        }

        trix-toolbar .trix-button[data-trix-attribute="highlight"].trix-active {
            background-color: yellow;
        }

        trix-editor h1 {
            font-size: 2em;
            margin-top: 1em;
            margin-bottom: 0.5em;
        }

        trix-editor h2 {
            font-size: 1.5em;
            margin-top: 0.83em;
            margin-bottom: 0.42em;
        }

        trix-editor h3 {
            font-size: 1.17em;
            margin-top: 0.67em;
            margin-bottom: 0.33em;
        }

        trix-editor [data-trix-attribute="highlight"] {
            background-color: yellow;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select
            new TomSelect('#categories', {
                plugins: ['remove_button'],
                create: false
            });

            // Trix Editor Customization
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

            // Prevent file uploads
            document.addEventListener('trix-file-accept', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endpush