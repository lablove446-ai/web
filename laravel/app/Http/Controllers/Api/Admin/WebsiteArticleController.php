<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Resources\WebsiteArticleResource;
use App\Models\WebsiteArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $articles = WebsiteArticle::with(['authorProfile', 'featuredImage', 'seo'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('category'), fn ($q, $c) => $q->where('category', $c))
            ->latest('published_at')
            ->paginate(20);

        return WebsiteArticleResource::collection($articles)->response();
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $article = WebsiteArticle::create($data);
        $article->load(['authorProfile', 'featuredImage', 'seo']);

        return (new WebsiteArticleResource($article))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteArticle $article): JsonResponse
    {
        $article->load(['authorProfile', 'featuredImage', 'seo']);

        return response()->json(new WebsiteArticleResource($article));
    }

    public function update(StoreArticleRequest $request, WebsiteArticle $article): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $article->update($data);
        $article->load(['authorProfile', 'featuredImage', 'seo']);

        return response()->json(new WebsiteArticleResource($article));
    }

    public function archive(WebsiteArticle $article): JsonResponse
    {
        $article->update(['status' => 'archived']);

        return response()->json(['message' => 'Article archived']);
    }

    public function destroy(WebsiteArticle $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted']);
    }
}
