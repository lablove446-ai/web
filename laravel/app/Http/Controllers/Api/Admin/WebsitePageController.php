<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Http\Resources\WebsitePageResource;
use App\Models\WebsitePage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsitePageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pages = WebsitePage::with(['featuredImage', 'seo'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsitePageResource::collection($pages)->response();
    }

    public function store(StorePageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $page = WebsitePage::create($data);
        $page->load(['featuredImage', 'seo']);

        return (new WebsitePageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsitePage $page): JsonResponse
    {
        $page->load(['featuredImage', 'seo']);

        return response()->json(new WebsitePageResource($page));
    }

    public function update(UpdatePageRequest $request, WebsitePage $page): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $page->update($data);
        $page->load(['featuredImage', 'seo']);

        return response()->json(new WebsitePageResource($page));
    }

    public function archive(WebsitePage $page): JsonResponse
    {
        $page->update(['status' => 'archived']);

        return response()->json(['message' => 'Page archived']);
    }

    public function destroy(WebsitePage $page): JsonResponse
    {
        $page->delete();

        return response()->json(['message' => 'Page deleted']);
    }
}
