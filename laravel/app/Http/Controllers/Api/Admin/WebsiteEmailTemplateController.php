<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmailTemplateRequest;
use App\Http\Resources\WebsiteEmailTemplateResource;
use App\Models\WebsiteEmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteEmailTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        $templates = WebsiteEmailTemplate::latest()->get();

        return WebsiteEmailTemplateResource::collection($templates)->response();
    }

    public function show(WebsiteEmailTemplate $template): JsonResponse
    {
        return response()->json(new WebsiteEmailTemplateResource($template));
    }

    public function store(StoreEmailTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $template = WebsiteEmailTemplate::create($data);

        return (new WebsiteEmailTemplateResource($template))
            ->response()
            ->setStatusCode(201);
    }

    public function update(StoreEmailTemplateRequest $request, WebsiteEmailTemplate $template): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $template->update($data);

        return response()->json(new WebsiteEmailTemplateResource($template));
    }

    public function toggleEnabled(WebsiteEmailTemplate $template): JsonResponse
    {
        $template->update(['is_enabled' => !$template->is_enabled]);

        return response()->json([
            'message' => $template->is_enabled ? 'Template enabled' : 'Template disabled',
            'is_enabled' => $template->is_enabled,
        ]);
    }

    public function reset(WebsiteEmailTemplate $template): JsonResponse
    {
        // Reset to default template body stored in config or a seed file
        $defaults = config('website.email_templates', []);
        $key = $template->key;

        if (isset($defaults[$key])) {
            $template->update([
                'subject' => $defaults[$key]['subject'] ?? $template->subject,
                'body' => $defaults[$key]['body'] ?? $template->body,
            ]);
        }

        return response()->json(new WebsiteEmailTemplateResource($template));
    }
}
