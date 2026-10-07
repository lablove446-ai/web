<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactEnquiryRequest;
use App\Models\WebsiteArticle;
use App\Models\WebsiteEvent;
use App\Models\WebsiteGallery;
use App\Models\WebsitePage;
use App\Models\WebsiteProject;
use App\Models\WebsitePublicProfile;
use App\Models\WebsiteResource;
use App\Models\WebsiteWelfareProgramme;
use App\Models\WebsiteContactEnquiry;
use App\Models\WebsiteSetting;
use App\Models\WebsiteNavigationItem;
use App\Models\WebsiteAnnouncement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicWebsiteController extends Controller
{
    public function homepage(): JsonResponse
    {
        $settings = WebsiteSetting::where('is_public', true)->pluck('value', 'key');

        $homepage = [
            'organization' => [
                'name' => $settings->get('organization_name', 'Kirinyaga Health Care Workers Welfare'),
                'short_name' => $settings->get('organization_short_name', 'KHCWW'),
                'tagline' => $settings->get('organization_tagline'),
                'description' => $settings->get('organization_description'),
                'mission' => $settings->get('organization_mission'),
                'vision' => $settings->get('organization_vision'),
                'email' => $settings->get('organization_email'),
                'phone' => $settings->get('organization_phone'),
                'address' => $settings->get('organization_address'),
                'county' => $settings->get('organization_county'),
                'country' => $settings->get('organization_country'),
            ],
            'sections' => [
                'hero' => [
                    'title' => $settings->get('homepage_hero_title', 'Care for those who care.'),
                    'subtitle' => $settings->get('homepage_hero_subtitle'),
                    'image_id' => $settings->get('homepage_hero_image_id'),
                    'cta_label' => $settings->get('homepage_hero_cta_label', 'Become a member'),
                    'cta_url' => $settings->get('homepage_hero_cta_url', '/register'),
                ],
                'featured_projects' => WebsiteProject::published()->featured()->limit(3)->orderBy('sort_order')->get(),
                'latest_articles' => WebsiteArticle::published()->latest('published_at')->limit(3)->get(),
                'upcoming_events' => WebsiteEvent::published()->where('start_at', '>=', now())->orderBy('start_at')->limit(3)->get(),
                'leadership' => WebsitePublicProfile::published()->orderBy('sort_order')->limit(4)->get(),
            ],
            'announcements' => WebsiteAnnouncement::active()->orderBy('sort_order')->get(),
        ];

        return response()->json($homepage);
    }

    public function settings(): JsonResponse
    {
        $settings = WebsiteSetting::where('is_public', true)->pluck('value', 'key');
        $navigation = WebsiteNavigationItem::with('children')
            ->whereNull('parent_id')
            ->visible()
            ->get();

        return response()->json([
            'settings' => $settings,
            'navigation' => $navigation,
            'social_links' => [
                'facebook' => $settings->get('social_facebook'),
                'instagram' => $settings->get('social_instagram'),
                'twitter' => $settings->get('social_twitter'),
                'linkedin' => $settings->get('social_linkedin'),
                'youtube' => $settings->get('social_youtube'),
                'whatsapp' => $settings->get('social_whatsapp'),
            ],
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        $page = WebsitePage::published()->with(['featuredImage', 'seo'])
            ->where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json($page);
    }

    public function projects(Request $request): JsonResponse
    {
        $projects = WebsiteProject::published()->with(['featuredImage', 'seo'])
            ->when($request->input('category'), fn ($q, $c) => $q->where('category', $c))
            ->orderBy('sort_order')
            ->paginate(12);

        return response()->json($projects);
    }

    public function project(string $slug): JsonResponse
    {
        $project = WebsiteProject::published()->with(['featuredImage', 'seo'])
            ->where('slug', $slug)->first();

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        return response()->json($project);
    }

    public function articles(Request $request): JsonResponse
    {
        $articles = WebsiteArticle::published()->with(['authorProfile', 'featuredImage', 'seo'])
            ->when($request->input('category'), fn ($q, $c) => $q->where('category', $c))
            ->latest('published_at')
            ->paginate(12);

        return response()->json($articles);
    }

    public function article(string $slug): JsonResponse
    {
        $article = WebsiteArticle::published()->with(['authorProfile', 'featuredImage', 'seo'])
            ->where('slug', $slug)->first();

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($article);
    }

    public function events(Request $request): JsonResponse
    {
        $events = WebsiteEvent::published()->with(['featuredImage', 'seo'])
            ->when($request->boolean('upcoming'), fn ($q) => $q->where('start_at', '>=', now()))
            ->orderBy('start_at')
            ->paginate(12);

        return response()->json($events);
    }

    public function event(string $slug): JsonResponse
    {
        $event = WebsiteEvent::published()->with(['featuredImage', 'seo'])
            ->where('slug', $slug)->first();

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    public function welfare(): JsonResponse
    {
        $programmes = WebsiteWelfareProgramme::published()->with(['featuredImage'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($programmes);
    }

    public function leadership(): JsonResponse
    {
        $profiles = WebsitePublicProfile::published()
            ->with(['profileImage', 'coverImage', 'seo'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($profiles);
    }

    public function profile(string $slug): JsonResponse
    {
        $profile = WebsitePublicProfile::published()
            ->with(['profileImage', 'coverImage', 'seo'])
            ->where('slug', $slug)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $articles = WebsiteArticle::published()
            ->where('author_profile_id', $profile->id)
            ->latest('published_at')
            ->limit(5)->get();

        return response()->json([
            'profile' => $profile,
            'related_articles' => $articles,
        ]);
    }

    public function gallery(Request $request): JsonResponse
    {
        $galleries = WebsiteGallery::published()->with(['coverImage'])
            ->when($request->input('type'), fn ($q, $t) => $q->where('album_type', $t))
            ->orderBy('sort_order')
            ->paginate(12);

        return response()->json($galleries);
    }

    public function galleryShow(string $slug): JsonResponse
    {
        $gallery = WebsiteGallery::published()->with(['items', 'coverImage'])
            ->where('slug', $slug)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        return response()->json($gallery);
    }

    public function resources(): JsonResponse
    {
        $resources = WebsiteResource::published()->with(['media'])
            ->orderBy('sort_order')
            ->paginate(20);

        return response()->json($resources);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => ['required', 'string', 'min:1', 'max:200']]);

        $query = $request->input('q');
        $results = [];

        $projects = WebsiteProject::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%"))
            ->limit(5)->get();
        $projects->each(fn ($p) => $results[] = ['type' => 'Project', 'title' => $p->title, 'slug' => $p->slug, 'excerpt' => $p->excerpt]);

        $articles = WebsiteArticle::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%"))
            ->limit(5)->get();
        $articles->each(fn ($a) => $results[] = ['type' => 'Article', 'title' => $a->title, 'slug' => $a->slug, 'excerpt' => $a->excerpt]);

        $events = WebsiteEvent::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")->orWhere('excerpt', 'like', "%{$query}%"))
            ->limit(5)->get();
        $events->each(fn ($e) => $results[] = ['type' => 'Event', 'title' => $e->title, 'slug' => $e->slug, 'excerpt' => $e->excerpt]);

        $profiles = WebsitePublicProfile::published()
            ->where(fn ($q) => $q->where('full_name', 'like', "%{$query}%")->orWhere('public_role', 'like', "%{$query}%"))
            ->limit(5)->get();
        $profiles->each(fn ($p) => $results[] = ['type' => 'Person', 'title' => $p->display_name, 'slug' => $p->slug, 'excerpt' => $p->short_bio]);

        $resources = WebsiteResource::published()
            ->where('title', 'like', "%{$query}%")
            ->limit(5)->get();
        $resources->each(fn ($r) => $results[] = ['type' => 'Resource', 'title' => $r->title, 'slug' => $r->slug, 'excerpt' => $r->description]);

        return response()->json([
            'query' => $query,
            'total' => count($results),
            'results' => $results,
        ]);
    }

    public function contactEnquiry(StoreContactEnquiryRequest $request): JsonResponse
    {
        if ($request->isHoneypotTriggered()) {
            return response()->json(['message' => 'Enquiry submitted'], 200);
        }

        $enquiry = WebsiteContactEnquiry::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'category' => $request->input('category'),
            'message' => $request->input('message'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Dispatch email notification using existing Laravel mail system
        // Mail::to($enquiry->email)->send(new ContactEnquiryAcknowledgement($enquiry));

        return response()->json([
            'message' => 'Your message has been received. A member of the KHCWW team will be in touch.',
            'reference' => $enquiry->reference,
        ], 201);
    }
}
