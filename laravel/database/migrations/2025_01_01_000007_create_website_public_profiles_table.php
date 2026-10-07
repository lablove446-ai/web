<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_public_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title_prefix')->nullable(); // Dr., Mr., Ms., Prof., etc.
            $table->string('full_name');
            $table->string('display_name')->nullable();
            $table->string('public_role')->nullable();
            $table->text('short_bio')->nullable();
            $table->longText('biography')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('professional_interests')->nullable();
            $table->string('department')->nullable();
            $table->string('workplace')->nullable();
            $table->string('public_email')->nullable();
            $table->string('public_phone')->nullable();
            $table->json('social_links')->nullable();
            $table->string('image_source')->default('existing_profile_image'); // existing_profile_image, public_media
            $table->foreignId('profile_image_id')->nullable()->constrained('website_media')->nullOnDelete();
            $table->foreignId('cover_image_id')->nullable()->constrained('website_media')->nullOnDelete();
            $table->boolean('published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('canonical_url')->nullable();
            $table->boolean('sitemap_included')->default(true);
            $table->string('structured_data_type')->default('ProfilePage');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_public_profiles');
    }
};
