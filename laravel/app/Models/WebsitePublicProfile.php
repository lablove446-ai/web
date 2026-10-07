<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsitePublicProfile extends Model
{
    use HasSeo, Sluggable, SoftDeletes;

    protected $table = 'website_public_profiles';

    protected $guarded = ['id'];

    protected $casts = [
        'social_links' => 'array',
        'published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function bootSluggable(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->full_name ?? $model->display_name);
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function profileImage(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'profile_image_id');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'cover_image_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function getDisplayNameAttribute(): ?string
    {
        $prefix = $this->title_prefix ? $this->title_prefix . ' ' : '';

        return $prefix . ($this->attributes['display_name'] ?? $this->full_name);
    }
}
