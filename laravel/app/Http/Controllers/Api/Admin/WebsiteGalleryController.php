<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteGalleryResource;
use App\Models\WebsiteGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $galleries = WebsiteGallery::with(['coverImage', 'items'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('type'), fn ($q, $t) => $q->where('album_type', $t))
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsiteGalleryResource::collection($galleries)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:website_galleries,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'album_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'cover_image_id' => ['nullable', 'exists:website_media,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();

        $gallery = WebsiteGallery::create($validated);

        return (new WebsiteGalleryResource($gallery))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteGallery $gallery): JsonResponse
    {
        $gallery->load(['coverImage', 'items']);

        return response()->json(new WebsiteGalleryResource($gallery));
    }

    public function update(Request $request, WebsiteGallery $gallery): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', "unique:website_galleries,slug,{$gallery->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'album_type' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,published,scheduled,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer'],
            'cover_image_id' => ['nullable', 'exists:website_media,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $gallery->update($validated);
        $gallery->load(['coverImage', 'items']);

        return response()->json(new WebsiteGalleryResource($gallery));
    }

    public function addItems(Request $request, WebsiteGallery $gallery): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.media_id' => ['required', 'exists:website_media,id'],
            'items.*.caption' => ['nullable', 'string', 'max:300'],
            'items.*.sort_order' => ['integer'],
        ]);

        foreach ($validated['items'] as $item) {
            $gallery->items()->attach($item['media_id'], [
                'caption' => $item['caption'] ?? null,
                'sort_order' => $item['sort_order'] ?? 0,
            ]);
        }

        $gallery->load(['items']);

        return response()->json(new WebsiteGalleryResource($gallery));
    }

    public function removeItem(WebsiteGallery $gallery, int $mediaId): JsonResponse
    {
        $gallery->items()->detach($mediaId);

        return response()->json(['message' => 'Item removed']);
    }

    public function destroy(WebsiteGallery $gallery): JsonResponse
    {
        $gallery->delete();

        return response()->json(['message' => 'Gallery deleted']);
    }
}
