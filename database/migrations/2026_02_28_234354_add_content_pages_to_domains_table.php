<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            // About Us
            $table->longText('about_us')->nullable()->after('google_tag_manager_id');
            $table->string('about_us_meta_title')->nullable()->after('about_us');
            $table->text('about_us_meta_description')->nullable()->after('about_us_meta_title');
            $table->string('about_us_canonical_url')->nullable()->after('about_us_meta_description');

            // Privacy Policy
            $table->longText('privacy_policy')->nullable()->after('about_us_canonical_url');
            $table->string('privacy_policy_meta_title')->nullable()->after('privacy_policy');
            $table->text('privacy_policy_meta_description')->nullable()->after('privacy_policy_meta_title');
            $table->string('privacy_policy_canonical_url')->nullable()->after('privacy_policy_meta_description');

            // Terms & Conditions
            $table->longText('terms_conditions')->nullable()->after('privacy_policy_canonical_url');
            $table->string('terms_conditions_meta_title')->nullable()->after('terms_conditions');
            $table->text('terms_conditions_meta_description')->nullable()->after('terms_conditions_meta_title');
            $table->string('terms_conditions_canonical_url')->nullable()->after('terms_conditions_meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn([
                'about_us', 'about_us_meta_title', 'about_us_meta_description', 'about_us_canonical_url',
                'privacy_policy', 'privacy_policy_meta_title', 'privacy_policy_meta_description', 'privacy_policy_canonical_url',
                'terms_conditions', 'terms_conditions_meta_title', 'terms_conditions_meta_description', 'terms_conditions_canonical_url',
            ]);
        });
    }
};
