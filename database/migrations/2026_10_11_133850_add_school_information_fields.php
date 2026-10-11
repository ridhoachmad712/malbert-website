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
        Schema::table('school_profiles', function (Blueprint $table): void {
            $table->string('principal_name')->nullable();
            $table->text('principal_greeting')->nullable();
            $table->text('admission_intro')->nullable();
            $table->text('admission_requirements')->nullable();
            $table->text('admission_steps')->nullable();
            $table->text('admission_schedule')->nullable();
            $table->string('admission_url', 500)->nullable();
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->date('event_date')->nullable();
            $table->string('event_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn(['event_date', 'event_location']);
        });

        Schema::table('school_profiles', function (Blueprint $table): void {
            $table->dropColumn(['principal_name', 'principal_greeting', 'admission_intro', 'admission_requirements', 'admission_steps', 'admission_schedule', 'admission_url']);
        });
    }
};
