<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_hotel', function (Blueprint $table) {
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->unique(['deal_id', 'hotel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_hotel');
    }
};
