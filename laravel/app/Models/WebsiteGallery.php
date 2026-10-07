<?php

namespace App\Models;

use App\Traits\Publishable;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteGallery extends Model
{
    use Publishable, Sluggable, SoftDeletes;

    protected $table = 'website_galleries';

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'cover_image_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteMedia::class, 'website_gallery_items')
            ->withPivot('caption', 'sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
