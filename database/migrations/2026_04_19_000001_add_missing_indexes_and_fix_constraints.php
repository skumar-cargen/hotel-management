<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add index on amenities.category
        Schema::table('amenities', function (Blueprint $table) {
            $table->index('category', 'amenities_category_index');
        });

        // Add index on amenities.is_active
        Schema::table('amenities', function (Blueprint $table) {
            $table->index('is_active', 'amenities_is_active_index');
        });

        // Add index on payments.status
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'payments_status_index');
        });

        // Fix hotels.location_id FK: change cascadeOnDelete to restrictOnDelete
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Revert hotels.location_id FK back to cascadeOnDelete
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->cascadeOnDelete();
        });

        // Drop payments.status index
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_index');
        });

        // Drop amenities.is_active index
        Schema::table('amenities', function (Blueprint $table) {
            $table->dropIndex('amenities_is_active_index');
        });

        // Drop amenities.category index
        Schema::table('amenities', function (Blueprint $table) {
            $table->dropIndex('amenities_category_index');
        });
    }
};
