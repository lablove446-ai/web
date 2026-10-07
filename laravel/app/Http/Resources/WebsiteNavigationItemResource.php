<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteNavigationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'url' => $this->url,
            'page_slug' => $this->page_slug,
            'target_type' => $this->target_type,
            'location' => $this->location,
            'is_visible' => $this->is_visible,
            'sort_order' => $this->sort_order,
            'children' => WebsiteNavigationItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
