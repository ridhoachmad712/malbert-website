<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('school_profiles')->whereNotNull('home_sections')->orderBy('id')->chunkById(100, function ($profiles): void {
            foreach ($profiles as $profile) {
                $sections = json_decode($profile->home_sections, true);

                if (! is_array($sections)) {
                    continue;
                }

                $shortcutOrder = (int) ($sections['shortcuts']['order'] ?? 10);
                $principalSection = is_array($sections['principal'] ?? null) ? $sections['principal'] : [];
                $sections['principal'] = array_merge($principalSection, ['order' => min(999, $shortcutOrder + 1)]);

                DB::table('school_profiles')->where('id', $profile->id)->update([
                    'home_sections' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Previous per-school section orders cannot be recovered after this update.
    }
};
