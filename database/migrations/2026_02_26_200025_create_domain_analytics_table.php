<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('hotel_clicks')->default(0);
            $table->unsignedInteger('booking_starts')->default(0);
            $table->unsignedInteger('booking_completions')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->json('top_hotels')->nullable();
            $table->json('top_locations')->nullable();
            $table->json('traffic_sources')->nullable();
            $table->timestamps();

            $table->unique(['domain_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_analytics');
    }
};
