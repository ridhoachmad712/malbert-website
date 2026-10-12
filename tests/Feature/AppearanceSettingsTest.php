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

    public function test_admin_can_change_hero_background_logo_and_public_navigation(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $navigation = $this->navigationPayload();
        $navigation['news']['enabled'] = '0';
        $navigation['events']['location'] = 'primary';
        $navigation['events']['label'] = 'Jadwal sekolah';
        $navigation['events']['order'] = '15';

        $this->actingAs($admin)->put(route('manage.appearance.update'), $this->appearancePayload($navigation) + [
            'logo' => UploadedFile::fake()->image('logo.png'),
            'hero' => UploadedFile::fake()->image('sekolah.jpg', 1600, 900),
        ])->assertRedirect();

        $school = SchoolProfile::current()->fresh();
        $this->assertNotNull($school->logo_path);
        $this->assertNotNull($school->hero_path);
        Storage::disk('public')->assertExists([$school->logo_path, $school->hero_path]);

        $home = $this->get(route('home'))->assertOk()->assertSee('Belajar bersama keluarga')->assertSee('hero-position-right');
        $this->assertStringContainsString('class="home-hero-background"', $home->getContent());
        $this->assertStringContainsString(asset('storage/'.$school->hero_path), $home->getContent());
        preg_match('/<nav id="site-nav".*?<\/nav>/s', $home->getContent(), $matches);
        $this->assertStringContainsString('Jadwal sekolah', $matches[0]);
        $this->assertStringNotContainsString('href="'.route('news').'"', $matches[0]);
        $this->assertLessThan(strpos($matches[0], 'Jadwal sekolah'), strpos($matches[0], 'Beranda'));
        $this->get(route('profile'))->assertSee('Jadwal sekolah');

        $hiddenHeroButton = $this->appearancePayload($navigation);
        $hiddenHeroButton['hero_primary_enabled'] = '0';
        $hiddenHeroButton['remove_logo'] = '1';
        $hiddenHeroButton['remove_hero'] = '1';
        $this->put(route('manage.appearance.update'), $hiddenHeroButton)->assertRedirect();
        $this->assertNull($school->fresh()->logo_path);
        $this->assertNull($school->fresh()->hero_path);
        $this->get(route('home'))->assertDontSee('class="home-hero-background"', false)->assertDontSee('Lihat agenda');
    }

    public function test_only_admin_can_change_appearance_and_invalid_menu_layout_is_rejected(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $this->actingAs($writer)->get(route('manage.appearance'))->assertForbidden();
        $this->actingAs($writer)->put(route('manage.appearance.update'), [])->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $navigation = $this->navigationPayload();
        $navigation['programs']['location'] = 'primary';
        $navigation['facilities']['location'] = 'primary';
        $this->actingAs($admin)->put(route('manage.appearance.update'), $this->appearancePayload($navigation))
            ->assertSessionHasErrors('navigation_items');
        $this->assertNull(SchoolProfile::current()->fresh()->navigation_items);
    }

    private function navigationPayload(): array
    {
        $items = [];
        foreach (SchoolProfile::NAVIGATION_DEFAULTS as $key => $defaults) {
            $items[$key] = [
                'label' => $defaults['label'],
                'enabled' => '1',
                'location' => $defaults['location'],
                'order' => (string) $defaults['order'],
            ];
        }

        return $items;
    }

    private function appearancePayload(array $navigation): array
    {
        return [
            'hero_title' => 'Belajar bersama keluarga',
            'hero_highlight' => 'di sekolah kami.',
            'hero_description' => 'Informasi kegiatan sekolah.',
            'hero_primary_label' => 'Lihat agenda',
            'hero_primary_route' => 'events',
            'hero_primary_enabled' => '1',
            'hero_secondary_label' => 'Profil kami',
            'hero_secondary_route' => 'profile',
            'hero_secondary_enabled' => '1',
            'hero_image_position' => 'right',
            'hero_overlay' => 'strong',
            'navigation_more_label' => 'Jelajahi',
            'navigation_items' => $navigation,
        ];
    }
}
