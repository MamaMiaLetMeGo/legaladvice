@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Category: {{ $category->name }}</h1>
                <p class="mt-2 text-sm text-gray-700">
                    @if($category->posts_count > 0)
                        This category contains {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}.
                    @else
                        This category has no posts yet.
                    @endif
                </p>
            </div>
            <div class="flex">
                <a href="{{ $category->url }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Category
                </a>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="mt-6 space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <div class="space-y-8 divide-y divide-gray-200">
                <div class="pt-8">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        {{-- Name --}}
                        <div class="sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Name
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $category->name) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="sm:col-span-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700">
                                Slug
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="slug" 
                                       id="slug" 
                                       value="{{ old('slug', $category->slug) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50" 
                                       readonly>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">URL-friendly version of the name. This is auto-generated.</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Color --}}
                        <div class="sm:col-span-2">
                            <label for="color" class="block text-sm font-medium text-gray-700">
                                Color
                            </label>
                            <div class="mt-1">
                                <input type="color" 
                                       name="color" 
                                       id="color" 
                                       value="{{ old('color', $category->color) }}"
                                       class="h-9 p-0 block w-full border-gray-300 rounded-md">
                            </div>
                            @error('color')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea name="description" 
                                          id="description" 
                                          rows="3"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Brief description of the category.</p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Icon --}}
                        <div class="sm:col-span-4">
                            <label for="icon" class="block text-sm font-medium text-gray-700">
                                Icon
                            </label>
                            <div class="mt-1">
                                <select name="icon" 
                                        id="icon" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select an icon</option>
                                    @foreach($icons ?? [] as $value => $label)
                                        <option value="{{ $value }}" {{ old('icon', $category->icon) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('icon')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Featured Toggle --}}
                        <div class="sm:col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           name="is_featured" 
                                           id="is_featured" 
                                           value="1"
                                           {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_featured" class="font-medium text-gray-700">Feature this category</label>
                                    <p class="text-gray-500">Featured categories are highlighted on the blog homepage.</p>
                                </div>
                            </div>
                            @error('is_featured')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SEO Section --}}
                        <div class="sm:col-span-6">
                            <div class="bg-gray-50 px-4 py-5 sm:rounded-lg sm:p-6">
                                <div class="md:grid md:grid-cols-3 md:gap-6">
                                    <div class="md:col-span-1">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">SEO</h3>
                                        <p class="mt-1 text-sm text-gray-500">Search engine optimization settings.</p>
                                    </div>
                                    <div class="mt-5 space-y-6 md:mt-0 md:col-span-2">
                                        <div>
                                            <label for="meta_title" class="block text-sm font-medium text-gray-700">
                                                Meta Title
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" 
                                                       name="meta_title" 
                                                       id="meta_title" 
                                                       value="{{ old('meta_title', $category->meta_title) }}"
                                                       maxlength="60"
                                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Maximum 60 characters.</p>
                                            @error('meta_title')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="meta_description" class="block text-sm font-medium text-gray-700">
                                                Meta Description
                                            </label>
                                            <div class="mt-1">
                                                <textarea name="meta_description" 
                                                          id="meta_description" 
                                                          rows="3"
                                                          maxlength="160"
                                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('meta_description', $category->meta_description) }}</textarea>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Maximum 160 characters.</p>
                                            @error('meta_description')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}"
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview meta title and description lengths
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescInput = document.getElementById('meta_description');

    function updateCharCount(input, maxLength) {
        const charCount = input.value.length;
        const remainingChars = maxLength - charCount;
        const helpText = input.nextElementSibling;
        helpText.textContent = `${remainingChars} characters remaining`;
        if (remainingChars < 10) {
            helpText.classList.add('text-yellow-600');
        } else {
            helpText.classList.remove('text-yellow-600');
        }
    }

    metaTitleInput.addEventListener('input', () => updateCharCount(metaTitleInput, 60));
    metaDescInput.addEventListener('input', () => updateCharCount(metaDescInput, 160));

    // Initialize character counts
    updateCharCount(metaTitleInput, 60);
    updateCharCount(metaDescInput, 160);

    // Auto-generate meta title from name if empty
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('slug').value = slug;
        
        if (!metaTitleInput.value) {
            metaTitleInput.value = this.value;
            updateCharCount(metaTitleInput, 60);
        }
    });
</script>
@endpush