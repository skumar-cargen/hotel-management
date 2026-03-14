<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_price_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field');
            $table->string('new_value');
            $table->timestamp('scheduled_at')->index();
            $table->timestamp('executed_at')->nullable();
            $table->enum('status', ['pending', 'executed', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_price_updates');
    }
};
