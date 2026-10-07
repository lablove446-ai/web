<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\JsonResponse;

class StructuredDataController extends Controller
{
    public function organization(): JsonResponse
    {
        $settings = WebsiteSetting::where('is_public', true)->pluck('value', 'key');

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->get('organization_name', 'Kirinyaga Health Care Workers Welfare'),
            'alternateName' => $settings->get('organization_short_name', 'KHCWW'),
            'description' => $settings->get('organization_description'),
            'url' => $settings->get('site_url'),
            'email' => $settings->get('organization_email') ? 'mailto:' . $settings->get('organization_email') : null,
            'telephone' => $settings->get('organization_phone'),
        ];

        $address = $settings->get('organization_address');
        if ($address) {
            $data['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => $settings->get('organization_county'),
                'addressCountry' => $settings->get('organization_country', 'KE'),
            ];
        }

        $sameAs = array_filter([
            $settings->get('social_facebook'),
            $settings->get('social_instagram'),
            $settings->get('social_twitter'),
            $settings->get('social_linkedin'),
            $settings->get('social_youtube'),
        ]);

        if (!empty($sameAs)) {
            $data['sameAs'] = array_values($sameAs);
        }

        // Remove null values
        $data = array_filter($data, fn ($v) => $v !== null);

        return response()->json($data);
    }
}
