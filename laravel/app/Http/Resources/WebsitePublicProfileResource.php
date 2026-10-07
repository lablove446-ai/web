<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsitePublicProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'member_id' => $this->member_id,
            'title_prefix' => $this->title_prefix,
            'full_name' => $this->full_name,
            'display_name' => $this->display_name,
            'public_role' => $this->public_role,
            'short_bio' => $this->short_bio,
            'biography' => $this->biography,
            'qualifications' => $this->qualifications,
            'professional_interests' => $this->professional_interests,
            'department' => $this->department,
            'workplace' => $this->workplace,
            'public_email' => $this->public_email,
            'public_phone' => $this->public_phone,
            'social_links' => $this->social_links,
            'image_source' => $this->image_source,
            'profile_image' => WebsiteMediaResource::make($this->whenLoaded('profileImage')),
            'cover_image' => WebsiteMediaResource::make($this->whenLoaded('coverImage')),
            'published' => $this->published,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'canonical_url' => $this->canonical_url,
            'sitemap_included' => $this->sitemap_included,
            'structured_data_type' => $this->structured_data_type,
            'seo' => $this->whenLoaded('seo', fn () => $this->getSeoData()),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
