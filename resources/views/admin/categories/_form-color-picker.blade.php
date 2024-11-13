<div class="sm:col-span-2">
    <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
    <div class="mt-1 flex space-x-2">
        <input type="color" 
               name="color" 
               id="color" 
               value="{{ old('color', $category->color ?? '#3B82F6') }}"
               class="h-9 p-0 block border-gray-300 rounded-md">
        <div id="colorPreview" 
             class="flex-1 rounded-md p-2 text-center text-sm font-medium">
            Preview Text
        </div>
    </div>
    @error('color')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>