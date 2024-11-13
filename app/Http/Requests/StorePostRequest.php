<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createPost');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body_content' => ['required', 'string'],
            'featured_image' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB
                    ->dimensions(minWidth: 600, minHeight: 400),
            ],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'status' => ['required', 'in:draft,published'],
            'video_url' => ['nullable', 'url'],
            'published_date' => ['nullable', 'date'],
        ];
    }
}