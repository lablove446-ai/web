<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteWelfareProgrammeResource;
use App\Models\WebsiteWelfareProgramme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteWelfareProgrammeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $programmes = WebsiteWelfareProgramme::with(['featuredImage'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsiteWelfareProgrammeResource::collection($programmes)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_welfare_programmes,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'featured_image_id' => ['nullable', 'exists:website_media,id'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();

        $programme = WebsiteWelfareProgramme::create($validated);

        return (new WebsiteWelfareProgrammeResource($programme))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteWelfareProgramme $programme): JsonResponse
    {
        $programme->load(['featuredImage']);

        return response()->json(new WebsiteWelfareProgrammeResource($programme));
    }

    public function update(Request $request, WebsiteWelfareProgramme $programme): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', "unique:website_welfare_programmes,slug,{$programme->id}"],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'featured_image_id' => ['nullable', 'exists:website_media,id'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'sitemap_included' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $programme->update($validated);
        $programme->load(['featuredImage']);

        return response()->json(new WebsiteWelfareProgrammeResource($programme));
    }

    public function destroy(WebsiteWelfareProgramme $programme): JsonResponse
    {
        $programme->delete();

        return response()->json(['message' => 'Programme deleted']);
    }
}
