<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteSeoSetting extends Model
{
    protected $table = 'website_seo_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'structured_data' => 'array',
        'sitemap_included' => 'boolean',
    ];

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'og_image_id');
    }

    public function entity()
    {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }
}
