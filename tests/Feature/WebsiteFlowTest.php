<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_without_school_data(): void
    {
        foreach (['/', '/profil', '/guru', '/berita', '/pengumuman', '/galeri', '/kontak', '/masuk'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_draft_news_is_hidden_until_publisher_approves_it(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $editor = User::factory()->create(['role' => 'editor']);
        $post = Post::create(['user_id' => $writer->id, 'type' => 'news', 'title' => 'Kegiatan Membaca', 'slug' => 'kegiatan-membaca', 'body' => 'Kegiatan belajar yang telah diperiksa.', 'status' => 'draft']);

        $this->get('/berita')->assertDontSee('Kegiatan Membaca');
        $this->get('/berita/kegiatan-membaca')->assertNotFound();
        $this->actingAs($writer)->post(route('manage.posts.status', $post), ['action' => 'review'])->assertRedirect();
        $this->get('/berita')->assertDontSee('Kegiatan Membaca');
        $this->actingAs($editor)->post(route('manage.posts.status', $post), ['action' => 'publish'])->assertRedirect();
        $this->get('/berita')->assertSee('Kegiatan Membaca');
        $this->get('/berita/kegiatan-membaca')->assertOk();
    }

    public function test_writer_cannot_publish_or_edit_another_authors_post(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $other = User::factory()->create(['role' => 'writer']);
        $post = Post::create(['user_id' => $other->id, 'type' => 'news', 'title' => 'Berita', 'slug' => 'berita-test', 'body' => 'Isi', 'status' => 'draft']);

        $this->actingAs($writer)->post(route('manage.posts.status', $post), ['action' => 'publish'])->assertForbidden();
        $this->actingAs($writer)->get(route('manage.posts.edit', $post))->assertForbidden();
        $this->actingAs($writer)->get(route('manage.profile'))->assertForbidden();
    }

    public function test_teacher_profile_requires_publication_approval(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('manage.teachers.store'), ['name' => 'Guru Contoh', 'position' => 'Wali kelas', 'is_published' => '1'])->assertRedirect(route('manage.teachers'));
        $teacher = Teacher::firstOrFail();
        $this->assertFalse($teacher->is_published);
        $this->get(route('teacher', $teacher->slug))->assertNotFound();

        $this->actingAs($admin)->put(route('manage.teachers.update', $teacher), ['name' => 'Guru Contoh', 'position' => 'Wali kelas', 'publication_approved' => '1', 'is_published' => '1'])->assertRedirect(route('manage.teachers'));
        $this->get(route('teacher', $teacher->slug))->assertOk();
    }

    public function test_sitemap_only_contains_public_content(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        Post::create(['user_id' => $writer->id, 'type' => 'news', 'title' => 'Draf', 'slug' => 'draf', 'body' => 'Isi', 'status' => 'draft']);
        Post::create(['user_id' => $writer->id, 'type' => 'news', 'title' => 'Terbit', 'slug' => 'terbit', 'body' => 'Isi', 'status' => 'published', 'published_at' => now()->subDay()]);
        Teacher::create(['name' => 'Guru Privat', 'slug' => 'guru-privat', 'position' => 'Guru', 'is_published' => true, 'publication_approved' => false]);

        $this->get(route('sitemap'))->assertOk()->assertSee('/berita/terbit')->assertDontSee('/berita/draf')->assertDontSee('/guru/guru-privat');
        $this->get(route('robots'))->assertOk()->assertSee('Disallow: /kelola');
    }

    public function test_only_authenticated_users_can_access_management(): void
    {
        $this->get(route('manage.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_add_a_writer_but_writer_cannot_manage_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('manage.users.store'), [
            'name' => 'Penulis Situs', 'email' => 'penulis@example.test', 'role' => 'writer',
            'password' => 'kata-sandi-panjang-sekali', 'password_confirmation' => 'kata-sandi-panjang-sekali',
        ])->assertRedirect();
        $writer = User::where('email', 'penulis@example.test')->firstOrFail();
        $this->assertSame('writer', $writer->role);
        $this->actingAs($writer)->get(route('manage.users'))->assertForbidden();
    }

    public function test_preview_is_private_and_guest_login_redirects_authenticated_user(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $post = Post::create(['user_id' => $writer->id, 'type' => 'news', 'title' => 'Draf Privat', 'slug' => 'draf-privat', 'body' => 'Isi draf', 'status' => 'draft']);

        $this->get(route('manage.posts.preview', $post))->assertRedirect(route('login'));
        $this->actingAs($writer)->get(route('manage.posts.preview', $post))->assertOk()->assertSee('Pratinjau privat');
        $this->actingAs($writer)->get(route('login'))->assertRedirect(route('manage.dashboard'));
    }
}
