<?php

// File: app/Http/Requests/Admin/PostStoreRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts'],
            'body_content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'], // 2MB max
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_date' => ['nullable', 'date'],
            'breadcrumb' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Please select at least one category.',
            'featured_image.max' => 'The featured image must not be larger than 2MB.',
        ];
    }
}