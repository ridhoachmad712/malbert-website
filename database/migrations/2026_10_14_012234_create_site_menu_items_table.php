<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('system_key')->nullable()->unique();
            $table->string('label', 40);
            $table->string('target_type', 20)->default('route');
            $table->string('target_route')->nullable();
            $table->foreignId('site_page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_url', 500)->nullable();
            $table->string('location', 20)->default('more');
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->boolean('is_enabled')->default(true);
            $table->boolean('show_in_footer')->default(false);
            $table->boolean('open_new_tab')->default(false);
            $table->timestamps();
        });

        $defaults = [
            'home' => ['Beranda', 'primary', 10], 'profile' => ['Profil', 'primary', 20],
            'teachers' => ['Guru', 'primary', 30], 'news' => ['Berita', 'primary', 40],
            'programs' => ['Program', 'more', 10], 'facilities' => ['Fasilitas', 'more', 20],
            'achievements' => ['Prestasi', 'more', 30], 'gallery' => ['Galeri', 'more', 40],
            'announcements' => ['Pengumuman', 'more', 50], 'events' => ['Agenda', 'more', 60],
            'faq' => ['Tanya jawab', 'more', 70], 'contact' => ['Kontak', 'more', 80],
            'aksikita' => ['AksiKita', 'primary', 50], 'admissions' => ['Penerimaan murid', 'cta', 10],
        ];
        $profile = DB::table('school_profiles')->first();
        $saved = json_decode($profile?->navigation_items ?: '{}', true) ?: [];
        $now = now();
        foreach ($defaults as $key => [$label, $location, $order]) {
            $item = $saved[$key] ?? [];
            DB::table('site_menu_items')->insert([
                'system_key' => $key,
                'label' => $item['label'] ?? $label,
                'target_type' => $key === 'aksikita' ? 'external' : 'route',
                'target_route' => $key === 'aksikita' ? null : $key,
                'external_url' => $key === 'aksikita' ? $profile?->aksikita_url : null,
                'location' => $item['location'] ?? $location,
                'sort_order' => $item['order'] ?? $order,
                'is_enabled' => ($item['enabled'] ?? true) && ($key !== 'aksikita' || $profile?->aksikita_url),
                'show_in_footer' => in_array($key, ['profile', 'programs', 'facilities', 'events', 'admissions', 'faq'], true),
                'open_new_tab' => $key === 'aksikita',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_menu_items');
    }
};
