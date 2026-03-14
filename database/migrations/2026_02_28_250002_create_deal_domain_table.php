<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_domain', function (Blueprint $table) {
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->unique(['deal_id', 'domain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_domain');
    }
};
