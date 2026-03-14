<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_email')->index();
            $table->string('guest_phone')->nullable();
            $table->string('guest_nationality')->nullable();
            $table->date('check_in_date')->index();
            $table->date('check_out_date');
            $table->unsignedSmallInteger('num_nights');
            $table->unsignedTinyInteger('num_adults')->default(1);
            $table->unsignedTinyInteger('num_children')->default(0);
            $table->unsignedTinyInteger('num_rooms')->default(1);
            $table->text('special_requests')->nullable();
            $table->decimal('room_price_per_night', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(5);
            $table->decimal('tourism_fee', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->index();
            $table->string('currency', 3)->default('AED');
            $table->enum('status', ['pending', 'paid', 'confirmed', 'cancelled', 'refunded'])->default('pending')->index();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('booked_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('domain_id');
            $table->index('hotel_id');
            $table->index(['domain_id', 'status']);
            $table->index(['hotel_id', 'check_in_date']);
            $table->index(['status', 'booked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
