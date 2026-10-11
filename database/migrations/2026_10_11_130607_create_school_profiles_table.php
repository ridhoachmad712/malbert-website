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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Nama sekolah belum diisi');
            $table->string('tagline')->nullable();
            $table->string('npsn', 8)->nullable();
            $table->text('description')->nullable();
            $table->text('history')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('programs')->nullable();
            $table->text('facilities')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('hours')->nullable();
            $table->string('map_url')->nullable();
            $table->string('aksikita_url')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('hero_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
