<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_hotel', function (Blueprint $table) {
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unique(['domain_id', 'hotel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_hotel');
    }
};
