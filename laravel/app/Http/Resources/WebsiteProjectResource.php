<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category,
            'status' => $this->status,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'location' => $this->location,
            'impact' => $this->impact,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'featured_image' => WebsiteMediaResource::make($this->whenLoaded('featuredImage')),
            'canonical_url' => $this->canonical_url,
            'sitemap_included' => $this->sitemap_included,
            'structured_data_type' => $this->structured_data_type,
            'seo' => $this->whenLoaded('seo', fn () => $this->getSeoData()),
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
