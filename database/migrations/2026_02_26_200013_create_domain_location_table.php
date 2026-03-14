<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_location', function (Blueprint $table) {
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unique(['domain_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_location');
    }
};
