<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'filename' => $this->filename,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'title' => $this->title,
            'description' => $this->description,
            'credit' => $this->credit,
            'copyright' => $this->copyright,
            'source' => $this->source,
            'mime_type' => $this->mime_type,
            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,
            'visibility' => $this->visibility,
            'usage' => $this->usage,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
