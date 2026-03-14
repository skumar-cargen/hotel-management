<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_images', function (Blueprint $table) {
            $table->string('category')->default('general')->after('hotel_id');
            $table->index(['hotel_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::table('hotel_images', function (Blueprint $table) {
            $table->dropIndex(['hotel_id', 'category']);
            $table->dropColumn('category');
        });
    }
};
