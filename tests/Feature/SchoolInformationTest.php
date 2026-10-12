<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
