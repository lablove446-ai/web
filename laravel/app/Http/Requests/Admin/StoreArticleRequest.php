<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_articles,slug'],
            'category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'author_profile_id' => ['nullable', 'exists:website_public_profiles,id'],
            'featured_image_id' => ['nullable', 'exists:website_media,id'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
