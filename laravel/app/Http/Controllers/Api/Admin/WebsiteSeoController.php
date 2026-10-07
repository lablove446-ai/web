<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSeoRequest;
use App\Http\Resources\WebsiteSeoSettingResource;
use App\Models\WebsiteSeoSetting;
use App\Services\SeoHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteSeoController extends Controller
{
    public function organization(Request $request): JsonResponse
    {
        $seo = WebsiteSeoSetting::firstOrCreate(
            ['entity_type' => 'global', 'entity_id' => null],
            ['structured_data_type' => 'Organization']
        );

        if ($request->isMethod('PUT')) {
            $validated = $request->validate([
                'seo_title' => ['nullable', 'string', 'max:200'],
                'meta_description' => ['nullable', 'string', 'max:500'],
                'og_title' => ['nullable', 'string', 'max:200'],
                'og_description' => ['nullable', 'string', 'max:500'],
                'og_image_id' => ['nullable', 'exists:website_media,id'],
                'canonical_url' => ['nullable', 'string', 'max:500'],
                'robots' => ['nullable', 'string', 'max:100'],
                'structured_data' => ['nullable', 'array'],
            ]);

            $seo->update($validated);
        }

        $seo->load(['ogImage']);

        return response()->json(new WebsiteSeoSettingResource($seo));
    }

    public function updateForEntity(UpdateSeoRequest $request, string $entityType, int $entityId): JsonResponse
    {
        $seo = WebsiteSeoSetting::firstOrCreate(
            ['entity_type' => $entityType, 'entity_id' => $entityId],
        );

        $seo->update($request->validated());
        $seo->load(['ogImage']);

        return response()->json(new WebsiteSeoSettingResource($seo));
    }

    public function health(SeoHealthService $seoHealth): JsonResponse
    {
        return response()->json([
            'score' => $seoHealth->calculateOverallScore(),
            'breakdown' => $seoHealth->getBreakdown(),
            'issues' => $seoHealth->getIssues(),
        ]);
    }

    public function searchConsole(Request $request): JsonResponse
    {
        $settings = [
            'verification_method' => \App\Models\WebsiteSetting::get('search_console_verification_method'),
            'verification_token' => \App\Models\WebsiteSetting::get('search_console_verification_token'),
            'google_analytics_id' => \App\Models\WebsiteSetting::get('google_analytics_id'),
            'is_verified' => false, // Never claim verification; this is operator-controlled
        ];

        if ($request->isMethod('PUT')) {
            $validated = $request->validate([
                'verification_method' => ['nullable', 'string', 'in:meta_tag,dns,html_file'],
                'verification_token' => ['nullable', 'string', 'max:500'],
                'google_analytics_id' => ['nullable', 'string', 'max:50'],
            ]);

            foreach ($validated as $key => $value) {
                \App\Models\WebsiteSetting::set("search_console_{$key}", $value, 'seo', false);
            }

            $settings = array_merge($settings, $validated);
        }

        return response()->json($settings);
    }
}
