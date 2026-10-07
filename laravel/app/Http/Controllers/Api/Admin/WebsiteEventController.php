<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Resources\WebsiteEventResource;
use App\Models\WebsiteEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = WebsiteEvent::with(['featuredImage', 'seo'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->boolean('upcoming'), fn ($q) => $q->where('start_at', '>=', now()))
            ->orderBy('start_at')
            ->paginate(20);

        return WebsiteEventResource::collection($events)->response();
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $event = WebsiteEvent::create($data);
        $event->load(['featuredImage', 'seo']);

        return (new WebsiteEventResource($event))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WebsiteEvent $event): JsonResponse
    {
        $event->load(['featuredImage', 'seo']);

        return response()->json(new WebsiteEventResource($event));
    }

    public function update(StoreEventRequest $request, WebsiteEvent $event): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $event->update($data);
        $event->load(['featuredImage', 'seo']);

        return response()->json(new WebsiteEventResource($event));
    }

    public function archive(WebsiteEvent $event): JsonResponse
    {
        $event->update(['status' => 'archived']);

        return response()->json(['message' => 'Event archived']);
    }

    public function destroy(WebsiteEvent $event): JsonResponse
    {
        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }
}
