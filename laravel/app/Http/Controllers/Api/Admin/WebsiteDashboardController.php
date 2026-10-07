<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteArticle;
use App\Models\WebsiteContactEnquiry;
use App\Models\WebsiteEvent;
use App\Models\WebsiteGallery;
use App\Models\WebsiteMedia;
use App\Models\WebsitePage;
use App\Models\WebsiteProject;
use App\Models\WebsitePublicProfile;
use App\Models\WebsiteResource;
use App\Services\SeoHealthService;
use Illuminate\Http\JsonResponse;

class WebsiteDashboardController extends Controller
{
    public function index(SeoHealthService $seoHealth): JsonResponse
    {
        $publishedPages = WebsitePage::where('status', 'published')->count();
        $draftPages = WebsitePage::where('status', 'draft')->count();
        $projects = WebsiteProject::where('status', 'published')->count();
        $articles = WebsiteArticle::where('status', 'published')->count();
        $events = WebsiteEvent::where('status', 'published')->count();
        $upcomingEvents = WebsiteEvent::where('status', 'published')->where('start_at', '>=', now())->count();
        $profiles = WebsitePublicProfile::where('published', true)->count();
        $galleries = WebsiteGallery::where('status', 'published')->count();
        $mediaCount = WebsiteMedia::count();
        $enquiries = WebsiteContactEnquiry::where('status', 'new')->count();
        $resources = WebsiteResource::where('status', 'published')->count();

        $seoScore = $seoHealth->calculateOverallScore();
        $seoIssues = $seoHealth->getIssues();

        return response()->json([
            'content' => [
                'published_pages' => $publishedPages,
                'draft_pages' => $draftPages,
                'projects' => $projects,
                'articles' => $articles,
                'events' => $events,
                'upcoming_events' => $upcomingEvents,
                'profiles' => $profiles,
                'galleries' => $galleries,
                'resources' => $resources,
            ],
            'media_count' => $mediaCount,
            'new_enquiries' => $enquiries,
            'seo' => [
                'score' => $seoScore,
                'issues' => $seoIssues,
            ],
            'recent_activity' => $this->recentActivity(),
        ]);
    }

    private function recentActivity(): array
    {
        $activities = collect();

        WebsitePage::latest()->limit(2)->get()
            ->each(fn ($p) => $activities->push(['type' => 'page', 'title' => $p->title, 'action' => 'updated', 'at' => $p->updated_at]));

        WebsiteProject::latest()->limit(2)->get()
            ->each(fn ($p) => $activities->push(['type' => 'project', 'title' => $p->title, 'action' => 'updated', 'at' => $p->updated_at]));

        WebsiteArticle::latest()->limit(2)->get()
            ->each(fn ($a) => $activities->push(['type' => 'article', 'title' => $a->title, 'action' => 'updated', 'at' => $a->updated_at]));

        return $activities->sortByDesc('at')->values()->take(8)->toArray();
    }
}
