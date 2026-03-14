<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('max_guests')->default(2);
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(0);
            $table->string('bed_type')->nullable();
            $table->decimal('room_size_sqm', 8, 2)->nullable();
            $table->decimal('base_price', 10, 2)->default(0)->index();
            $table->unsignedInteger('total_rooms')->default(1);
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('hotel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
