<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteGalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'album_type' => $this->album_type,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'cover_image' => WebsiteMediaResource::make($this->whenLoaded('coverImage')),
            'items' => WebsiteMediaResource::collection($this->whenLoaded('items')),
            'canonical_url' => $this->canonical_url,
            'sitemap_included' => $this->sitemap_included,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
