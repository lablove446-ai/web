<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteContactEnquiry extends Model
{
    protected $table = 'website_contact_enquiries';

    protected $guarded = ['id'];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    protected static function booted(): void
    {
        static::creating(function ($enquiry) {
            $enquiry->reference = 'KHCWW-' . strtoupper(\Illuminate\Support\Str::random(8));
        });
    }
}
