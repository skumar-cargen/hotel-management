<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('star_rating')->default(3)->index();
            $table->longText('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->boolean('is_beach_access')->default(false)->index();
            $table->boolean('is_family_friendly')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->json('faq_data')->nullable();
            $table->integer('sort_order')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->decimal('min_price', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('location_id');
            $table->index(['location_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
