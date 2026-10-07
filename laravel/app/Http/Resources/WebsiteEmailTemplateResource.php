<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteEmailTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'is_enabled' => $this->is_enabled,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
