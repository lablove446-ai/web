<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_projects,slug'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:draft,published,scheduled,archived'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:200'],
            'impact' => ['nullable', 'string', 'max:200'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'featured_image_id' => ['nullable', 'exists:website_media,id'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
