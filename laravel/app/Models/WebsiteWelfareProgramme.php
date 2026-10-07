<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteWelfareProgramme extends Model
{
    use Sluggable, SoftDeletes;

    use Publishable {
        Publishable::scopePublished as scopePublishedBase;
    }

    public function scopePublished($query)
    {
        return $this->scopePublishedBase($query);
    }

    protected $table = 'website_welfare_programmes';

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'featured_image_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
