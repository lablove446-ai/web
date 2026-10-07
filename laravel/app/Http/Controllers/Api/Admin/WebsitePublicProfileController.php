<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePublicProfileRequest;
use App\Http\Resources\WebsitePublicProfileResource;
use App\Models\User;
use App\Models\WebsitePublicProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsitePublicProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $profiles = WebsitePublicProfile::with(['member', 'profileImage', 'coverImage', 'seo'])
            ->when($request->has('published'), fn ($q) => $q->where('published', $request->boolean('published')))
            ->when($request->input('search'), function ($q, $search) {
                $q->where(fn ($sub) => $sub->where('full_name', 'like', "%{$search}%")
                    ->orWhere('public_role', 'like', "%{$search}%"));
            })
            ->orderBy('sort_order')
            ->paginate(20);

        return WebsitePublicProfileResource::collection($profiles)->response();
    }

    public function searchMembers(Request $request): JsonResponse
    {
        $request->validate(['q' => ['required', 'string', 'min:1', 'max:200']]);

        $members = User::where('name', 'like', '%' . $request->input('q') . '%')
            ->orWhere('email', 'like', '%' . $request->input('q') . '%')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($members);
    }

    public function store(StorePublicProfileRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $profile = WebsitePublicProfile::create($data);
        $profile->load(['member', 'profileImage', 'coverImage', 'seo']);

        return (new WebsitePublicProfileResource($profile))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsitePublicProfile $profile): JsonResponse
    {
        $profile->load(['member', 'profileImage', 'coverImage', 'seo']);

        return response()->json(new WebsitePublicProfileResource($profile));
    }

    public function update(StorePublicProfileRequest $request, WebsitePublicProfile $profile): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $profile->update($data);
        $profile->load(['member', 'profileImage', 'coverImage', 'seo']);

        return response()->json(new WebsitePublicProfileResource($profile));
    }

    public function togglePublish(WebsitePublicProfile $profile): JsonResponse
    {
        $profile->update(['published' => !$profile->published]);

        return response()->json([
            'message' => $profile->published ? 'Profile published' : 'Profile unpublished',
            'published' => $profile->published,
        ]);
    }

    public function archive(WebsitePublicProfile $profile): JsonResponse
    {
        $profile->update(['published' => false]);
        $profile->delete();

        return response()->json(['message' => 'Profile archived']);
    }
}
