<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Resources\WebsiteMediaResource;
use App\Models\WebsiteMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteMediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $media = WebsiteMedia::when($request->input('visibility'), fn ($q, $v) => $q->where('visibility', $v))
            ->latest()
            ->paginate(24);

        return WebsiteMediaResource::collection($media)->response();
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('website-media', 'public');

        $media = WebsiteMedia::create([
            'filename' => $file->getClientOriginalName(),
            'disk' => 'public',
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => null,
            'height' => null,
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'credit' => $request->input('credit'),
            'copyright' => $request->input('copyright'),
            'source' => $request->input('source'),
            'visibility' => $request->input('visibility', 'public'),
            'uploaded_by' => auth()->id(),
        ]);

        return (new WebsiteMediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteMedia $media): JsonResponse
    {
        return response()->json(new WebsiteMediaResource($media));
    }

    public function update(Request $request, WebsiteMedia $media): JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:300'],
            'caption' => ['nullable', 'string', 'max:300'],
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credit' => ['nullable', 'string', 'max:200'],
            'copyright' => ['nullable', 'string', 'max:200'],
            'source' => ['nullable', 'string', 'max:200'],
            'visibility' => ['nullable', 'string', 'in:public,private'],
        ]);

        $media->update($validated);

        return response()->json(new WebsiteMediaResource($media));
    }

    public function destroy(WebsiteMedia $media): JsonResponse
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(['message' => 'Media deleted']);
    }
}
