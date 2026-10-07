<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteResource extends Model
{
    use Sluggable, SoftDeletes;

    protected $table = 'website_resources';

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(WebsiteMedia::class, 'media_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('is_public', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
