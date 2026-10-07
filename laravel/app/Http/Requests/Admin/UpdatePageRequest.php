<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('page')?->id ?? $this->route('id');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', "unique:website_pages,slug,{$pageId}"],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'template' => ['nullable', 'string', 'max:100'],
            'sections' => ['nullable', 'array'],
            'featured_image_id' => ['nullable', 'exists:website_media,id'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
            'structured_data_type' => ['nullable', 'string', 'max:50'],
            'published_at' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
