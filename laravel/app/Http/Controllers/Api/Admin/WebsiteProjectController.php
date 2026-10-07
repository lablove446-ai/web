<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Resources\WebsiteProjectResource;
use App\Models\WebsiteProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projects = WebsiteProject::with(['featuredImage', 'seo'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('category'), fn ($q, $c) => $q->where('category', $c))
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsiteProjectResource::collection($projects)->response();
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $project = WebsiteProject::create($data);
        $project->load(['featuredImage', 'seo']);

        return (new WebsiteProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteProject $project): JsonResponse
    {
        $project->load(['featuredImage', 'seo']);

        return response()->json(new WebsiteProjectResource($project));
    }

    public function update(StoreProjectRequest $request, WebsiteProject $project): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $project->update($data);
        $project->load(['featuredImage', 'seo']);

        return response()->json(new WebsiteProjectResource($project));
    }

    public function archive(WebsiteProject $project): JsonResponse
    {
        $project->update(['status' => 'archived']);

        return response()->json(['message' => 'Project archived']);
    }

    public function destroy(WebsiteProject $project): JsonResponse
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }
}
