<div class="sm:col-span-6">
    <label class="block text-sm font-medium text-gray-700">Category Image</label>
    <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
        <div class="space-y-1 text-center">
            <div class="flex flex-col items-center">
                <img id="imagePreview" class="h-32 w-32 object-cover rounded-lg mb-4 {{ isset($category) && $category->image ? '' : 'hidden' }}"
                     src="{{ isset($category) && $category->image ? Storage::url($category->image) : '' }}"
                     alt="Category preview">
                
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                
                <div class="flex text-sm text-gray-600">
                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                        <span>Upload a file</span>
                        <input id="image" name="image" type="file" class="sr-only" accept="image/jpeg,image/png,image/webp">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
            </div>
        </div>
    </div>
    @error('image')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>