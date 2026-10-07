<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteSeoSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'seo_title' => $this->seo_title,
            'meta_description' => $this->meta_description,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => WebsiteMediaResource::make($this->whenLoaded('ogImage')),
            'twitter_card' => $this->twitter_card,
            'twitter_title' => $this->twitter_title,
            'twitter_description' => $this->twitter_description,
            'canonical_url' => $this->canonical_url,
            'robots' => $this->robots,
            'focus_keyword' => $this->focus_keyword,
            'sitemap_included' => $this->sitemap_included,
            'structured_data_type' => $this->structured_data_type,
            'structured_data' => $this->structured_data,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
