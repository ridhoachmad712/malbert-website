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
            $table->string('hero_title')->nullable();
            $table->string('hero_highlight')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_label')->nullable();
            $table->string('hero_primary_route')->nullable();
            $table->boolean('hero_primary_enabled')->default(true);
            $table->string('hero_secondary_label')->nullable();
            $table->string('hero_secondary_route')->nullable();
            $table->boolean('hero_secondary_enabled')->default(true);
            $table->string('hero_image_position')->default('center');
            $table->string('hero_overlay')->default('normal');
            $table->string('navigation_more_label')->default('Informasi');
            $table->json('navigation_items')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title', 'hero_highlight', 'hero_description', 'hero_primary_label',
                'hero_primary_route', 'hero_primary_enabled', 'hero_secondary_label', 'hero_secondary_route', 'hero_secondary_enabled',
                'hero_image_position', 'hero_overlay', 'navigation_more_label', 'navigation_items',
            ]);
        });
    }
};
