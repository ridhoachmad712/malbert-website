<?php

namespace Tests\Feature;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppearanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_section_settings_move_principal_after_shortcuts(): void
    {
        $school = SchoolProfile::current();
        $school->update(['home_sections' => [
            'shortcuts' => ['enabled' => true, 'order' => 12],
            'updates' => ['enabled' => false, 'order' => 20],
            'principal' => ['enabled' => true, 'order' => 40],
        ]]);

        $migration = require database_path('migrations/2026_10_14_023042_move_principal_section_after_shortcuts.php');
        $migration->up();

        $school->refresh();
        $this->assertSame(13, $school->homeSection('principal')['order']);
        $this->assertFalse($school->homeSection('updates')['enabled']);
        $this->assertSame(11, SchoolProfile::HOME_SECTIONS['principal']['order']);
    }

    public function test_admin_can_change_hero_theme_and_home_sections(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('manage.appearance'))->assertOk()->assertSee('Bagian beranda');
        $payload = $this->appearancePayload();
        $payload['logo'] = UploadedFile::fake()->image('logo.png');
        $payload['hero'] = UploadedFile::fake()->image('hero.jpg');
        $payload['home_sections']['programs']['enabled'] = 0;

        $this->actingAs($admin)->put(route('manage.appearance.update'), $payload)->assertRedirect()->assertSessionHasNoErrors();

        $school = SchoolProfile::current()->fresh();
        Storage::disk('public')->assertExists($school->logo_path);
        Storage::disk('public')->assertExists($school->hero_path);
        $this->assertSame('external', $school->hero_primary_target_type);
        $this->assertSame('https://pendaftaran.example.sch.id', $school->hero_primary_external_url);
        $this->assertFalse($school->homeSection('programs')['enabled']);
        $this->get('/')->assertOk()->assertSee('https://pendaftaran.example.sch.id')->assertSee('--blue:#123456', false)->assertDontSee('home-programs');
    }

    public function test_only_admin_can_change_appearance_and_unsafe_external_url_is_rejected(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $this->actingAs($writer)->get(route('manage.appearance'))->assertForbidden();
        $this->actingAs($writer)->put(route('manage.appearance.update'), $this->appearancePayload())->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = $this->appearancePayload();
        $payload['hero_primary_external_url'] = 'javascript:alert(1)';
        $this->actingAs($admin)->put(route('manage.appearance.update'), $payload)->assertSessionHasErrors('hero_primary_external_url');
        $this->assertDatabaseMissing('school_profiles', ['hero_primary_external_url' => 'javascript:alert(1)']);
    }

    private function appearancePayload(): array
    {
        $sections = [];
        foreach (SchoolProfile::HOME_SECTIONS as $key => $defaults) {
            $sections[$key] = ['enabled' => 1, 'order' => $defaults['order']];
        }

        return [
            'hero_title' => 'Selamat datang', 'hero_highlight' => 'di sekolah kami',
            'hero_description' => 'Informasi resmi sekolah.', 'hero_primary_label' => 'Daftar sekarang',
            'hero_primary_target_type' => 'external', 'hero_primary_external_url' => 'https://pendaftaran.example.sch.id',
            'hero_primary_enabled' => 1, 'hero_secondary_label' => 'Profil sekolah',
            'hero_secondary_target_type' => 'route', 'hero_secondary_route' => 'profile', 'hero_secondary_enabled' => 1,
            'hero_image_position' => 'right', 'hero_overlay' => 'strong',
            'navigation_more_label' => 'Informasi', 'home_sections' => $sections,
            'theme_primary_color' => '#123456', 'theme_accent_color' => '#24765d',
            'theme_font' => 'system', 'header_style' => 'compact', 'card_style' => 'square', 'hero_style' => 'compact',
        ];
    }
}
