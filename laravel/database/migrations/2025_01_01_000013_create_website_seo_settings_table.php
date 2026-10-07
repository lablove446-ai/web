<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // page, project, article, event, profile, global
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->foreignId('og_image_id')->nullable()->constrained('website_media')->nullOnDelete();
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index, follow');
            $table->string('focus_keyword')->nullable();
            $table->boolean('sitemap_included')->default(true);
            $table->string('structured_data_type')->nullable();
            $table->json('structured_data')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_seo_settings');
    }
};
