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
                <label for="author_id" class="block text-sm font-medium text-gray-700">Author</label>
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
                                    {{ auth()->id() === $user->id ? 'selected' : '' }}
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
                    <textarea name="body_content" id="basic-example" cols="30" rows="10">{{ old('body_content') }}</textarea>
                   
                </div>
                @error('body_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image</label>
                <div class="mt-1 flex items-center">
                    <div class="featured-image-preview hidden relative">
                        <img src="" alt="Featured image preview" class="h-32 w-32 object-cover rounded-lg">
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
                <label for="video" class="block text-sm font-medium text-gray-700">Video</label>
                <div class="mt-1 flex items-center">
                    <div class="video-preview hidden relative">
                        <video 
                            width="320" 
                            height="240" 
                            controls 
                            class="rounded-lg"
                        >
                            <source src="" type="video/mp4">
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
                        accept="video/mp4,video/quicktime,video/*"
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
                        Select Video
                    </button>
                </div>
                @error('video')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="published_date" class="block text-sm font-medium text-gray-700">Published Date</label>
                <input 
                    type="date" 
                    name="published_date" 
                    id="published_date" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                    value="{{ old('published_date', now()->format('Y-m-d')) }}"
                >
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
    <script src="https://cdn.tiny.cloud/1/ohrfrapuhu20w9tbmhnitg6kvecj2vouenborprjzguexqop/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-jquery@2/dist/tinymce-jquery.min.js"></script> --}}
    
    <script>
        // Move removeImage function outside to make it globally accessible
        function removeImage() {
            const fileInput = document.getElementById('featured_image');
            const preview = document.querySelector('.featured-image-preview');
            
            fileInput.value = '';
            preview.classList.add('hidden');
            preview.querySelector('img').src = '';
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
    

        // DOM ready handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select
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
                    
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                        preview.classList.remove('hidden');
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

        
        tinymce.init({
        selector: 'textarea#basic-example',
        content_style: `
            /* This will be applied in the editor */
            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                line-height: 1.5;
                font-size: 16px;
                color: #111827;
            }
            h1 { font-size: 2.25rem; font-weight: bold; margin: 1.5rem 0; }
            h2 { font-size: 1.875rem; font-weight: bold; margin: 1.25rem 0; }
            h3 { font-size: 1.5rem; font-weight: bold; margin: 1rem 0; }
            p { margin-bottom: 1rem; }
            ul { list-style-type: disc; padding-left: 2rem; margin-bottom: 1rem; }
            ol { list-style-type: decimal; padding-left: 2rem; margin-bottom: 1rem; }
            blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; font-style: italic; margin: 1rem 0; }
            code { background-color: #f3f4f6; padding: 0.25rem; border-radius: 0.25rem; font-size: 0.875rem; }
            .highlight { background-color: #fef3c7; padding: 0 0.25rem; border-radius: 0.25rem; }
        `,
        formats: {
            highlight: { inline: 'span', classes: 'highlight' }
        },
        height: 500,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image media | help',
        media_live_embeds: true,
        automatic_uploads: true,

        // Image Upload Handler
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/upload-image'); // Laravel endpoint

    // Include CSRF Token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    xhr.setRequestHeader('X-CSRF-Token', token);

    // Monitor upload progress
    xhr.upload.onprogress = (e) => {
      progress((e.loaded / e.total) * 100);
    };

    // Handle the server response
    xhr.onload = () => {
      if (xhr.status !== 200) {
        reject({ message: `HTTP Error: ${xhr.status}`, remove: true });
        return;
      }

      const json = JSON.parse(xhr.responseText);
      if (!json || typeof json.location !== 'string') {
        reject('Invalid JSON response: ' + xhr.responseText);
        return;
      }

      resolve(json.location); // Pass the image URL back to TinyMCE
    };

    // Handle errors
    xhr.onerror = () => {
      reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };

    // Prepare the file upload
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());

    // Send the upload request
    xhr.send(formData);
  }),

        // File Picker for Video Uploads
        file_picker_types: 'media',
        file_picker_callback: function(callback, value, meta) {
            if (meta.filetype === 'media') {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'video/*');

            input.onchange = function() {
                const file = input.files[0];
                const formData = new FormData();
                formData.append('file', file);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/upload-video', true);

                // Set CSRF Token Header
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                xhr.setRequestHeader('X-CSRF-Token', token);

                xhr.onload = function() {
                if (xhr.status === 200) {
                    const json = JSON.parse(xhr.responseText);
                    callback(json.location, { title: file.name });
                } else {
                    alert('Failed to upload video: ' + xhr.statusText);
                }
                };

                xhr.send(formData);
            };

            input.click();
            }
        },

        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });

    </script>
@endpush