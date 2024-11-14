<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Later we can add proper authorization
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug' . ($this->category ? ',' . $this->category->id : '')],
            'description' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB max
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}