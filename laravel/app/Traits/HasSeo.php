<?php

namespace App\Traits;

use App\Models\WebsiteSeoSetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(WebsiteSeoSetting::class, 'entity');
    }

    public function getSeoData(): array
    {
        $seo = $this->seo;

        return [
            'seo_title' => $seo?->seo_title,
            'meta_description' => $seo?->meta_description,
            'og_title' => $seo?->og_title,
            'og_description' => $seo?->og_description,
            'og_image' => $seo?->ogImage?->url,
            'twitter_card' => $seo?->twitter_card ?? 'summary_large_image',
            'canonical_url' => $seo?->canonical_url ?? $this->canonical_url,
            'robots' => $seo?->robots ?? 'index, follow',
            'focus_keyword' => $seo?->focus_keyword,
            'sitemap_included' => $seo?->sitemap_included ?? true,
            'structured_data_type' => $seo?->structured_data_type ?? $this->structured_data_type,
        ];
    }
}
