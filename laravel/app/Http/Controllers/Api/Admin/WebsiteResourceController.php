<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteResourceResource;
use App\Models\WebsiteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $resources = WebsiteResource::with(['media'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('type'), fn ($q, $t) => $q->where('resource_type', $t))
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsiteResourceResource::collection($resources)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_resources,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'resource_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'sort_order' => ['integer'],
            'media_id' => ['nullable', 'exists:website_media,id'],
            'download_url' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();

        $resource = WebsiteResource::create($validated);

        return (new WebsiteResourceResource($resource))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteResource $resource): JsonResponse
    {
        $resource->load(['media']);

        return response()->json(new WebsiteResourceResource($resource));
    }

    public function update(Request $request, WebsiteResource $resource): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', "unique:website_resources,slug,{$resource->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'resource_type' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,published,archived'],
            'sort_order' => ['integer'],
            'media_id' => ['nullable', 'exists:website_media,id'],
            'download_url' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $resource->update($validated);
        $resource->load(['media']);

        return response()->json(new WebsiteResourceResource($resource));
    }

    public function destroy(WebsiteResource $resource): JsonResponse
    {
        $resource->delete();

        return response()->json(['message' => 'Resource deleted']);
    }
}
