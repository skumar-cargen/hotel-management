<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_method')->nullable();
            $table->string('gateway')->default('mashreq');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->enum('status', ['initiated', 'processing', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('initiated');
            $table->json('gateway_response')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_transaction_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
