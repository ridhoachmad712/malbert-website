<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\DemoWebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DemoWebsiteSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_public_and_can_be_loaded_twice_without_duplicates(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->seed(DemoWebsiteSeeder::class);
        $this->assertSame(6, Teacher::count());
        $this->assertSame(16, Post::count());
        $this->assertSame($admin->id, Post::firstOrFail()->user_id);
        $this->assertTrue(Storage::disk('public')->exists('demo/hero.svg'));
        $this->get('/')->assertOk()->assertSee('SDI M Albert (Data Contoh)')->assertSee('Contoh: Pekan literasi');
        $this->get('/guru')->assertOk()->assertSee('Alya Pramesti (Contoh)');
        $this->get('/galeri')->assertOk()->assertSee('Contoh: Suasana pekan literasi');

        SchoolProfile::current()->update(['name' => 'Profil yang sudah disunting']);
        $this->seed(DemoWebsiteSeeder::class);

        $this->assertSame(6, Teacher::count());
        $this->assertSame(16, Post::count());
        $this->assertSame('Profil yang sudah disunting', SchoolProfile::current()->name);
        $this->assertSame(1, User::where('role', 'admin')->count());
    }

    public function test_demo_data_requires_an_existing_admin(): void
    {
        $this->expectException(RuntimeException::class);
        $this->seed(DemoWebsiteSeeder::class);
    }
}
