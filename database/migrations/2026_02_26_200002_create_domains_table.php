<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_primary')->default(false);
            $table->string('default_currency', 3)->default('AED');
            $table->string('default_language', 5)->default('en');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image_path')->nullable();
            $table->text('robots_txt')->nullable();
            $table->boolean('sitemap_enabled')->default(true);
            $table->string('google_analytics_id')->nullable();
            $table->string('google_search_console_verification')->nullable();
            $table->string('meta_pixel_id')->nullable();
            $table->string('google_tag_manager_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
