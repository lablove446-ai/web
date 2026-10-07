<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteEmailTemplate extends Model
{
    protected $table = 'website_email_templates';

    protected $guarded = ['id'];

    protected $casts = [
        'variables' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function renderVariables(array $data): string
    {
        $body = $this->body;

        foreach ($data as $key => $value) {
            $body = str_replace('{{' . $key . '}}', e($value), $body);
        }

        return $body;
    }
}
