<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:users,id'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_public_profiles,slug'],
            'title_prefix' => ['nullable', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:200'],
            'display_name' => ['nullable', 'string', 'max:200'],
            'public_role' => ['nullable', 'string', 'max:200'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string', 'max:1000'],
            'professional_interests' => ['nullable', 'string', 'max:1000'],
            'department' => ['nullable', 'string', 'max:200'],
            'workplace' => ['nullable', 'string', 'max:200'],
            'public_email' => ['nullable', 'email', 'max:200'],
            'public_phone' => ['nullable', 'string', 'max:30'],
            'social_links' => ['nullable', 'array'],
            'image_source' => ['required', 'string', 'in:existing_profile_image,public_media'],
            'profile_image_id' => ['nullable', 'exists:website_media,id'],
            'cover_image_id' => ['nullable', 'exists:website_media,id'],
            'published' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
        ];
    }
}
