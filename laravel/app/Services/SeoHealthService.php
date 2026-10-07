<?php

namespace App\Services;

use App\Models\WebsiteArticle;
use App\Models\WebsiteEvent;
use App\Models\WebsitePage;
use App\Models\WebsiteProject;
use App\Models\WebsitePublicProfile;
use App\Models\WebsiteSeoSetting;
use App\Models\WebsiteSetting;
use Illuminate\Support\Collection;

class SeoHealthService
{
    public function calculateOverallScore(): int
    {
        $breakdown = $this->getBreakdown();

        return (int) round(array_sum($breakdown) / max(count($breakdown), 1));
    }

    public function getBreakdown(): array
    {
        return [
            'organization' => $this->scoreOrganization(),
            'pages' => $this->scoreEntity(WebsitePage::published()->get(), 'page'),
            'projects' => $this->scoreEntity(WebsiteProject::published()->get(), 'project'),
            'articles' => $this->scoreEntity(WebsiteArticle::published()->get(), 'article'),
            'events' => $this->scoreEntity(WebsiteEvent::published()->get(), 'event'),
            'profiles' => $this->scoreProfiles(),
        ];
    }

    public function getIssues(): array
    {
        $issues = [];

        $this->collectEntityIssues(WebsitePage::published()->get(), 'page', $issues);
        $this->collectEntityIssues(WebsiteProject::published()->get(), 'project', $issues);
        $this->collectEntityIssues(WebsiteArticle::published()->get(), 'article', $issues);
        $this->collectEntityIssues(WebsiteEvent::published()->get(), 'event', $issues);

        $this->collectProfileIssues($issues);
        $this->collectOrganizationIssues($issues);

        return $issues;
    }

    private function scoreOrganization(): int
    {
        $score = 0;
        $total = 6;

        if (WebsiteSetting::get('organization_name')) $score++;
        if (WebsiteSetting::get('organization_description')) $score++;
        if (WebsiteSetting::get('organization_email')) $score++;
        if (WebsiteSetting::get('organization_phone')) $score++;
        if (WebsiteSetting::get('site_url')) $score++;
        if (WebsiteSetting::get('organization_address')) $score++;

        return (int) round(($score / $total) * 100);
    }

    private function scoreEntity(Collection $items, string $type): int
    {
        if ($items->isEmpty()) return 100;

        $total = 0;
        $max = 0;

        foreach ($items as $item) {
            $seo = WebsiteSeoSetting::where('entity_type', $type)->where('entity_id', $item->id)->first();

            $checks = [
                $seo?->seo_title || $item->title,
                $seo?->meta_description,
                $seo?->og_title || $item->title,
                $seo?->canonical_url || $item->canonical_url,
                $item->sitemap_included,
            ];

            $total += count(array_filter($checks));
            $max += count($checks);
        }

        return $max > 0 ? (int) round(($total / $max) * 100) : 0;
    }

    private function scoreProfiles(): int
    {
        $profiles = WebsitePublicProfile::published()->get();

        if ($profiles->isEmpty()) return 100;

        $total = 0;
        $max = 0;

        foreach ($profiles as $profile) {
            $checks = [
                $profile->full_name,
                $profile->public_role,
                $profile->short_bio,
                $profile->profile_image_id || $profile->image_source === 'existing_profile_image',
                $profile->sitemap_included,
            ];

            $total += count(array_filter($checks));
            $max += count($checks);
        }

        return $max > 0 ? (int) round(($total / $max) * 100) : 0;
    }

    private function collectEntityIssues(Collection $items, string $type, array &$issues): void
    {
        foreach ($items as $item) {
            $seo = WebsiteSeoSetting::where('entity_type', $type)->where('entity_id', $item->id)->first();

            if (!$seo?->meta_description) {
                $issues[] = [
                    'severity' => 'warning',
                    'entity_type' => $type,
                    'entity_id' => $item->id,
                    'entity_title' => $item->title,
                    'field' => 'meta_description',
                    'message' => "Missing meta description for {$type}: {$item->title}",
                ];
            }

            if (!$seo?->og_title && !$item->title) {
                $issues[] = [
                    'severity' => 'info',
                    'entity_type' => $type,
                    'entity_id' => $item->id,
                    'entity_title' => $item->title,
                    'field' => 'og_title',
                    'message' => "Missing Open Graph title for {$type}: {$item->title}",
                ];
            }

            if (!$item->sitemap_included) {
                $issues[] = [
                    'severity' => 'info',
                    'entity_type' => $type,
                    'entity_id' => $item->id,
                    'entity_title' => $item->title,
                    'field' => 'sitemap_included',
                    'message' => "Excluded from sitemap: {$type} {$item->title}",
                ];
            }
        }
    }

    private function collectProfileIssues(array &$issues): void
    {
        $profiles = WebsitePublicProfile::published()->get();

        foreach ($profiles as $profile) {
            if (!$profile->short_bio) {
                $issues[] = [
                    'severity' => 'warning',
                    'entity_type' => 'profile',
                    'entity_id' => $profile->id,
                    'entity_title' => $profile->full_name,
                    'field' => 'short_bio',
                    'message' => "Missing short bio for profile: {$profile->full_name}",
                ];
            }

            if (!$profile->public_role) {
                $issues[] = [
                    'severity' => 'info',
                    'entity_type' => 'profile',
                    'entity_id' => $profile->id,
                    'entity_title' => $profile->full_name,
                    'field' => 'public_role',
                    'message' => "Missing public role for profile: {$profile->full_name}",
                ];
            }
        }
    }

    private function collectOrganizationIssues(array &$issues): void
    {
        if (!WebsiteSetting::get('organization_description')) {
            $issues[] = [
                'severity' => 'warning',
                'entity_type' => 'global',
                'entity_id' => null,
                'entity_title' => 'Organization',
                'field' => 'organization_description',
                'message' => 'Missing organization description for structured data',
            ];
        }

        if (!WebsiteSetting::get('site_url')) {
            $issues[] = [
                'severity' => 'error',
                'entity_type' => 'global',
                'entity_id' => null,
                'entity_title' => 'Organization',
                'field' => 'site_url',
                'message' => 'Missing site URL — sitemap and canonical URLs cannot be generated',
            ];
        }
    }
}
