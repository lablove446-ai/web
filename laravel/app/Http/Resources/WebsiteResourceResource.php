<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'resource_type' => $this->resource_type,
            'status' => $this->status,
            'is_public' => $this->is_public,
            'sort_order' => $this->sort_order,
            'media' => WebsiteMediaResource::make($this->whenLoaded('media')),
            'download_url' => $this->download_url,
            'canonical_url' => $this->canonical_url,
            'sitemap_included' => $this->sitemap_included,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
