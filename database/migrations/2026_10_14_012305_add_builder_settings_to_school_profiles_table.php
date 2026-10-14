<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('hero_primary_target_type', 20)->default('route');
            $table->foreignId('hero_primary_page_id')->nullable()->constrained('site_pages')->nullOnDelete();
            $table->string('hero_primary_external_url', 500)->nullable();
            $table->string('hero_secondary_target_type', 20)->default('route');
            $table->foreignId('hero_secondary_page_id')->nullable()->constrained('site_pages')->nullOnDelete();
            $table->string('hero_secondary_external_url', 500)->nullable();
            $table->json('home_sections')->nullable();
            $table->string('theme_primary_color', 7)->default('#2459d8');
            $table->string('theme_accent_color', 7)->default('#1b7661');
            $table->string('theme_font', 20)->default('jakarta');
            $table->string('header_style', 20)->default('standard');
            $table->string('card_style', 20)->default('rounded');
            $table->string('hero_style', 20)->default('full');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hero_primary_page_id');
            $table->dropConstrainedForeignId('hero_secondary_page_id');
            $table->dropColumn([
                'hero_primary_target_type', 'hero_primary_external_url', 'hero_secondary_target_type',
                'hero_secondary_external_url', 'home_sections', 'theme_primary_color', 'theme_accent_color',
                'theme_font', 'header_style', 'card_style', 'hero_style',
            ]);
        });
    }
};
