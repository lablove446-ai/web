<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteNavigationItem extends Model
{
    protected $table = 'website_navigation_items';

    protected $guarded = ['id'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WebsiteNavigationItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WebsiteNavigationItem::class, 'parent_id')
            ->where('is_visible', true)
            ->orderBy('sort_order');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)->orderBy('sort_order');
    }
}
