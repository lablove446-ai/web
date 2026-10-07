<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\WebsiteNavigationItem;
use App\Models\WebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = WebsiteSetting::all()->groupBy('group');
        $navigation = WebsiteNavigationItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'settings' => $settings,
            'navigation' => $navigation,
        ]);
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        foreach ($request->input('settings') as $setting) {
            WebsiteSetting::set(
                $setting['key'],
                $setting['value'] ?? null,
                $setting['group'] ?? 'general',
                $setting['is_public'] ?? true,
            );
        }

        return response()->json(['message' => 'Settings updated']);
    }

    public function navigationIndex(): JsonResponse
    {
        $navigation = WebsiteNavigationItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return response()->json($navigation);
    }

    public function navigationUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['nullable', 'exists:website_navigation_items,id'],
            'items.*.label' => ['required', 'string', 'max:100'],
            'items.*.url' => ['nullable', 'string', 'max:500'],
            'items.*.page_slug' => ['nullable', 'string', 'max:200'],
            'items.*.target_type' => ['nullable', 'string', 'in:internal,external'],
            'items.*.location' => ['nullable', 'string', 'in:main,footer,topbar'],
            'items.*.is_visible' => ['boolean'],
            'items.*.sort_order' => ['integer'],
            'items.*.parent_id' => ['nullable', 'exists:website_navigation_items,id'],
        ]);

        foreach ($validated['items'] as $item) {
            if (isset($item['id'])) {
                WebsiteNavigationItem::where('id', $item['id'])->update($item);
            } else {
                WebsiteNavigationItem::create($item);
            }
        }

        return response()->json(['message' => 'Navigation updated']);
    }
}
