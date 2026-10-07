<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('url')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('credit')->nullable();
            $table->string('copyright')->nullable();
            $table->string('source')->nullable();
            $table->string('visibility')->default('public'); // public, private
            $table->string('usage')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('visibility');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_media');
    }
};
