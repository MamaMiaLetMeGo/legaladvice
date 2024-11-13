<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateAuthorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateProfile', $this->user());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_image' => [
                'nullable',
                File::image()
                    ->max(2 * 1024) // 2MB
                    ->dimensions(
                        minWidth: 200,
                        minHeight: 200,
                        maxWidth: 2000,
                        maxHeight: 2000
                    ),
            ],
            'social_links' => ['nullable', 'array'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_links.github' => ['nullable', 'url', 'max:255'],
        ];
    }
}