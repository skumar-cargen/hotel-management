<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domain_analytics', function (Blueprint $table) {
            $table->unsignedInteger('organic_traffic')->default(0)->after('traffic_sources');
            $table->unsignedInteger('search_impressions')->default(0)->after('organic_traffic');
            $table->unsignedInteger('search_clicks')->default(0)->after('search_impressions');
            $table->decimal('avg_position', 5, 2)->default(0)->after('search_clicks');
            $table->decimal('bounce_rate', 5, 2)->default(0)->after('avg_position');
            $table->unsignedInteger('avg_session_duration')->default(0)->after('bounce_rate');
            $table->json('top_keywords')->nullable()->after('avg_session_duration');
            $table->json('top_landing_pages')->nullable()->after('top_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('domain_analytics', function (Blueprint $table) {
            $table->dropColumn([
                'organic_traffic',
                'search_impressions',
                'search_clicks',
                'avg_position',
                'bounce_rate',
                'avg_session_duration',
                'top_keywords',
                'top_landing_pages',
            ]);
        });
    }
};
