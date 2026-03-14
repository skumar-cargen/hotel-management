<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('available_rooms')->default(0);
            $table->unsignedInteger('booked_rooms')->default(0);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['room_type_id', 'date']);
            $table->index(['room_type_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_availability');
    }
};
