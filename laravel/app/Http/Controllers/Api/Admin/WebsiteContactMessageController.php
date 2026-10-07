<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContactEnquiryResource;
use App\Models\WebsiteContactEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteContactMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $enquiries = WebsiteContactEnquiry::when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return WebsiteContactEnquiryResource::collection($enquiries)->response();
    }

    public function show(WebsiteContactEnquiry $enquiry): JsonResponse
    {
        if ($enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }

        return response()->json(new WebsiteContactEnquiryResource($enquiry));
    }

    public function updateStatus(Request $request, WebsiteContactEnquiry $enquiry): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,read,responded,archived'],
        ]);

        if ($validated['status'] === 'responded') {
            $validated['responded_at'] = now();
            $validated['responded_by'] = auth()->id();
        }

        $enquiry->update($validated);

        return response()->json(new WebsiteContactEnquiryResource($enquiry));
    }
}
