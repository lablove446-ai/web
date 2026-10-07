<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\WebsiteArticle;
use App\Models\WebsiteEvent;
use App\Models\WebsitePage;
use App\Models\WebsiteProject;
use App\Models\WebsitePublicProfile;
use App\Models\WebsiteResource;
use App\Models\WebsiteWelfareProgramme;
use App\Models\WebsiteSetting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = WebsiteSetting::get('site_url', config('app.url'));
        $urls = [];

        // Static public pages
        $staticRoutes = ['', '/about', '/work', '/projects', '/news', '/events', '/leadership', '/gallery', '/resources', '/contact'];
        foreach ($staticRoutes as $route) {
            $urls[] = ['loc' => $baseUrl . $route, 'lastmod' => now()->toDateString(), 'priority' => $route === '' ? '1.0' : '0.8'];
        }

        // Dynamic published content
        $this->addUrls($urls, $baseUrl, WebsitePage::published()->where('sitemap_included', true)->get(), 'pages');
        $this->addUrls($urls, $baseUrl, WebsiteProject::published()->where('sitemap_included', true)->get(), 'projects');
        $this->addUrls($urls, $baseUrl, WebsiteArticle::published()->where('sitemap_included', true)->get(), 'news');
        $this->addUrls($urls, $baseUrl, WebsiteEvent::published()->where('sitemap_included', true)->get(), 'events');
        $this->addUrls($urls, $baseUrl, WebsitePublicProfile::published()->where('sitemap_included', true)->get(), 'people');
        $this->addUrls($urls, $baseUrl, WebsiteResource::published()->where('sitemap_included', true)->get(), 'resources');
        $this->addUrls($urls, $baseUrl, WebsiteWelfareProgramme::published()->where('sitemap_included', true)->get(), 'welfare');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function addUrls(array &$urls, string $baseUrl, $items, string $prefix): void
    {
        foreach ($items as $item) {
            $urls[] = [
                'loc' => "{$baseUrl}/{$prefix}/{$item->slug}",
                'lastmod' => $item->updated_at?->toDateString() ?? now()->toDateString(),
                'priority' => '0.6',
            ];
        }
    }
}
