<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Publishable;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteProject extends Model
{
    use HasSeo, Publishable, Sluggable, SoftDeletes;

    protected $table = 'website_projects';

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

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
