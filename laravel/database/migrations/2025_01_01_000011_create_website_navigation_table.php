<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('page_slug')->nullable();
            $table->string('target_type')->default('internal'); // internal, external
            $table->string('location')->default('main'); // main, footer, topbar
            $table->foreignId('parent_id')->nullable()->constrained('website_navigation_items')->nullOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['location', 'is_visible', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_navigation_items');
    }
};
