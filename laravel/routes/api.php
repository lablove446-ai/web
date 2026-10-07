<?php

use App\Http\Controllers\Api\Admin\WebsiteArticleController;
use App\Http\Controllers\Api\Admin\WebsiteContactMessageController;
use App\Http\Controllers\Api\Admin\WebsiteDashboardController;
use App\Http\Controllers\Api\Admin\WebsiteEmailTemplateController;
use App\Http\Controllers\Api\Admin\WebsiteEventController;
use App\Http\Controllers\Api\Admin\WebsiteGalleryController;
use App\Http\Controllers\Api\Admin\WebsiteMediaController;
use App\Http\Controllers\Api\Admin\WebsitePageController;
use App\Http\Controllers\Api\Admin\WebsiteProjectController;
use App\Http\Controllers\Api\Admin\WebsitePublicProfileController;
use App\Http\Controllers\Api\Admin\WebsiteResourceController;
use App\Http\Controllers\Api\Admin\WebsiteSeoController;
use App\Http\Controllers\Api\Admin\WebsiteSettingsController;
use App\Http\Controllers\Api\Admin\WebsiteWelfareProgrammeController;
use App\Http\Controllers\Api\Public\PublicWebsiteController;
use App\Http\Controllers\Api\Public\RobotsController;
use App\Http\Controllers\Api\Public\SitemapController;
use App\Http\Controllers\Api\Public\StructuredDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('public/website')->group(function () {
    Route::get('homepage', [PublicWebsiteController::class, 'homepage']);
    Route::get('settings', [PublicWebsiteController::class, 'settings']);
    Route::get('pages/{slug}', [PublicWebsiteController::class, 'page']);
    Route::get('projects', [PublicWebsiteController::class, 'projects']);
    Route::get('projects/{slug}', [PublicWebsiteController::class, 'project']);
    Route::get('articles', [PublicWebsiteController::class, 'articles']);
    Route::get('articles/{slug}', [PublicWebsiteController::class, 'article']);
    Route::get('events', [PublicWebsiteController::class, 'events']);
    Route::get('events/{slug}', [PublicWebsiteController::class, 'event']);
    Route::get('welfare', [PublicWebsiteController::class, 'welfare']);
    Route::get('people', [PublicWebsiteController::class, 'leadership']);
    Route::get('people/{slug}', [PublicWebsiteController::class, 'profile']);
    Route::get('gallery', [PublicWebsiteController::class, 'gallery']);
    Route::get('gallery/{slug}', [PublicWebsiteController::class, 'galleryShow']);
    Route::get('resources', [PublicWebsiteController::class, 'resources']);
    Route::get('search', [PublicWebsiteController::class, 'search']);
});

Route::prefix('public')->group(function () {
    Route::post('contact-enquiries', [PublicWebsiteController::class, 'contactEnquiry']);
});

// XML / structured data endpoints
Route::get('sitemap.xml', [SitemapController::class, 'index']);
Route::get('robots.txt', [RobotsController::class, 'index']);
Route::get('structured-data/organization', [StructuredDataController::class, 'organization']);

/*
|--------------------------------------------------------------------------
| Admin Website Management API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin/website')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [WebsiteDashboardController::class, 'index']);

    // Pages
    Route::apiResource('pages', WebsitePageController::class);
    Route::post('pages/{page}/archive', [WebsitePageController::class, 'archive']);

    // Projects
    Route::apiResource('projects', WebsiteProjectController::class);
    Route::post('projects/{project}/archive', [WebsiteProjectController::class, 'archive']);

    // Articles
    Route::apiResource('articles', WebsiteArticleController::class);
    Route::post('articles/{article}/archive', [WebsiteArticleController::class, 'archive']);

    // Events
    Route::apiResource('events', WebsiteEventController::class);
    Route::post('events/{event}/archive', [WebsiteEventController::class, 'archive']);

    // Welfare programmes
    Route::apiResource('welfare', WebsiteWelfareProgrammeController::class);

    // Public profiles / people
    Route::get('people/search-members', [WebsitePublicProfileController::class, 'searchMembers']);
    Route::apiResource('people', WebsitePublicProfileController::class);
    Route::post('people/{profile}/toggle-publish', [WebsitePublicProfileController::class, 'togglePublish']);
    Route::post('people/{profile}/archive', [WebsitePublicProfileController::class, 'archive']);

    // Media library
    Route::apiResource('media', WebsiteMediaController::class);

    // Galleries
    Route::apiResource('gallery', WebsiteGalleryController::class);
    Route::post('gallery/{gallery}/items', [WebsiteGalleryController::class, 'addItems']);
    Route::delete('gallery/{gallery}/items/{mediaId}', [WebsiteGalleryController::class, 'removeItem']);

    // Resources
    Route::apiResource('resources', WebsiteResourceController::class);

    // Navigation & footer
    Route::get('navigation', [WebsiteSettingsController::class, 'navigationIndex']);
    Route::put('navigation', [WebsiteSettingsController::class, 'navigationUpdate']);
    Route::get('footer', [WebsiteSettingsController::class, 'navigationIndex']);
    Route::put('footer', [WebsiteSettingsController::class, 'navigationUpdate']);

    // Settings
    Route::get('settings', [WebsiteSettingsController::class, 'index']);
    Route::put('settings', [WebsiteSettingsController::class, 'update']);

    // SEO
    Route::get('seo/organization', [WebsiteSeoController::class, 'organization']);
    Route::put('seo/organization', [WebsiteSeoController::class, 'organization']);
    Route::put('seo/{entityType}/{entityId}', [WebsiteSeoController::class, 'updateForEntity']);
    Route::get('seo/health', [WebsiteSeoController::class, 'health']);
    Route::get('seo/search-console', [WebsiteSeoController::class, 'searchConsole']);
    Route::put('seo/search-console', [WebsiteSeoController::class, 'searchConsole']);

    // Email templates
    Route::apiResource('email-templates', WebsiteEmailTemplateController::class);
    Route::post('email-templates/{template}/toggle-enabled', [WebsiteEmailTemplateController::class, 'toggleEnabled']);
    Route::post('email-templates/{template}/reset', [WebsiteEmailTemplateController::class, 'reset']);

    // Contact messages
    Route::get('contact-messages', [WebsiteContactMessageController::class, 'index']);
    Route::get('contact-messages/{enquiry}', [WebsiteContactMessageController::class, 'show']);
    Route::patch('contact-messages/{enquiry}', [WebsiteContactMessageController::class, 'updateStatus']);
});
