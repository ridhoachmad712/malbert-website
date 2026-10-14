<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_public_pages_render_and_only_published_content_is_visible(): void
    {
        foreach (['/program', '/fasilitas', '/agenda', '/prestasi', '/penerimaan', '/faq'] as $path) {
            $this->get($path)->assertOk();
        }

        $author = User::factory()->create(['role' => 'editor']);
        $draft = Post::create(['user_id' => $author->id, 'type' => 'achievement', 'title' => 'Prestasi Rahasia', 'slug' => 'prestasi-rahasia', 'body' => 'Belum diperiksa', 'status' => 'draft']);
        $public = Post::create(['user_id' => $author->id, 'type' => 'achievement', 'title' => 'Prestasi Resmi', 'slug' => 'prestasi-resmi', 'body' => 'Sudah diperiksa', 'status' => 'published', 'published_at' => now()]);
        Post::create(['user_id' => $author->id, 'type' => 'faq', 'title' => 'Pertanyaan Rahasia?', 'slug' => 'pertanyaan-rahasia', 'body' => 'Belum dijawab', 'status' => 'draft']);
        Post::create(['user_id' => $author->id, 'type' => 'faq', 'title' => 'Bagaimana caranya?', 'slug' => 'bagaimana-caranya', 'body' => 'Hubungi sekolah.', 'status' => 'published', 'published_at' => now()]);

        $this->get('/prestasi')->assertSee($public->title)->assertDontSee($draft->title);
        $this->get($public->publicUrl())->assertOk();
        $this->get($draft->publicUrl())->assertNotFound();
        $this->get('/faq')->assertSee('Bagaimana caranya?')->assertDontSee('Pertanyaan Rahasia?');
        $this->get('/sitemap.xml')->assertSee('/prestasi/prestasi-resmi')->assertDontSee('/prestasi/prestasi-rahasia')->assertDontSee('/faq/bagaimana-caranya');
    }

    public function test_event_requires_date_and_profile_changes_preserve_school_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = SchoolProfile::current();
        $school->update(['name' => 'Sekolah Pilihan Pengguna']);

        $this->actingAs($admin)->post(route('manage.posts.store', 'event'), ['title' => 'Agenda Baru', 'body' => 'Isi agenda'])->assertSessionHasErrors('event_date');
        $this->actingAs($admin)->post(route('manage.posts.store', 'event'), ['title' => 'Agenda Baru', 'body' => 'Isi agenda', 'event_date' => now()->addDay()->toDateString()])->assertRedirect(route('manage.posts.type', 'event'));
        $this->assertSame(1, Post::where('type', 'event')->count());

        $this->actingAs($admin)->put(route('manage.profile.update'), [
            'name' => 'Sekolah Pilihan Pengguna', 'principal_name' => 'Kepala Sekolah Resmi', 'principal_greeting' => 'Selamat datang di sekolah kami.',
            'admission_intro' => 'Pendaftaran akan dibuka.', 'admission_steps' => 'Hubungi panitia.',
        ])->assertRedirect();

        $this->get('/profil')->assertSee('Kepala Sekolah Resmi');
        $this->get('/penerimaan')->assertSee('Pendaftaran akan dibuka.');
        $this->assertSame('Sekolah Pilihan Pengguna', SchoolProfile::current()->fresh()->name);
    }

    public function test_admin_can_show_and_remove_principal_png_in_greeting_section(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $school = SchoolProfile::current();

        $this->get('/')->assertDontSee('home-principal-portrait');
        $this->actingAs($admin)->put(route('manage.profile.update'), [
            'name' => $school->name,
            'principal_name' => 'Ibu Kepala Sekolah',
            'principal_greeting' => str_repeat('Selamat datang di sekolah kami. ', 20),
            'principal_photo' => UploadedFile::fake()->image('kepala-sekolah.png'),
            'principal_photo_height' => 260,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $school->refresh();
        Storage::disk('public')->assertExists($school->principal_photo_path);
        $photoPath = $school->principal_photo_path;
        $this->get('/')->assertOk()->assertSeeInOrder(['home-shortcuts', 'home-principal-portrait', 'home-principal-copy', 'home-principal-name', 'home-updates'])->assertSee('style="--principal-photo-height: 260px"', false)->assertSee($school->principal_greeting)->assertDontSee('home-hero-principal');
        $this->get(route('manage.profile'))->assertSee('name="principal_photo_height"', false)->assertSee('value="260"', false);

        $this->actingAs($admin)->put(route('manage.profile.update'), [
            'name' => $school->name,
            'principal_name' => $school->principal_name,
            'principal_greeting' => $school->principal_greeting,
            'remove_principal_photo' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($photoPath);
        $this->get('/')->assertDontSee('home-principal-portrait');
    }

    public function test_principal_photo_requires_png(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('manage.profile.update'), [
            'name' => 'Sekolah Contoh',
            'principal_photo' => UploadedFile::fake()->image('kepala-sekolah.jpg'),
        ])->assertSessionHasErrors('principal_photo');
        $this->assertNull(SchoolProfile::current()->principal_photo_path);
    }

    public function test_principal_photo_height_must_stay_within_allowed_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('manage.profile.update'), [
            'name' => 'Sekolah Contoh',
            'principal_photo_height' => 600,
        ])->assertSessionHasErrors('principal_photo_height');

        $this->assertSame(300, SchoolProfile::current()->principalPhotoHeight());
    }
}
