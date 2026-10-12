<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_each_content_type_has_its_own_creation_page_and_filtered_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('manage.posts.create'))->assertOk()->assertSee('Pilih jenis konten');

        foreach (['news', 'announcement', 'event', 'gallery', 'program', 'facility', 'achievement', 'faq'] as $type) {
            $this->get(route('manage.posts.create-type', $type))->assertOk()->assertSee('name="title"', false);
            $this->get(route('manage.posts.type', $type))->assertOk();
        }

        $this->get(route('manage.posts.create-type', 'unknown'))->assertNotFound();
        $this->get(route('manage.posts.create-type', 'event'))->assertSee('name="event_date"', false)->assertDontSee('name="gallery_images[]"', false);
        $this->get(route('manage.posts.create-type', 'gallery'))->assertSee('name="gallery_images[]"', false)->assertDontSee('name="event_date"', false);
        $this->get(route('manage.posts.create-type', 'faq'))->assertDontSee('name="cover"', false)->assertDontSee('name="summary"', false);
    }

    public function test_content_type_comes_from_route_and_type_specific_fields_are_validated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('manage.posts.store', 'event'), [
            'type' => 'news', 'title' => 'Rapat wali murid', 'body' => 'Rincian rapat.',
        ])->assertSessionHasErrors('event_date');

        $this->post(route('manage.posts.store', 'event'), [
            'type' => 'news', 'title' => 'Rapat wali murid', 'body' => 'Rincian rapat.',
            'event_date' => now()->addWeek()->toDateString(), 'is_featured' => '1',
        ])->assertRedirect(route('manage.posts.type', 'event'));
        $event = Post::firstOrFail();
        $this->assertSame('event', $event->type);
        $this->assertFalse($event->is_featured);

        Storage::fake('public');
        $this->post(route('manage.posts.store', 'faq'), [
            'title' => 'Di mana sekolah?', 'body' => 'Lihat halaman kontak.',
            'cover' => UploadedFile::fake()->image('abaikan.jpg'),
        ])->assertRedirect(route('manage.posts.type', 'faq'));
        $this->assertNull(Post::where('type', 'faq')->firstOrFail()->cover_path);

        $this->post(route('manage.posts.store', 'gallery'), ['title' => 'Galeri kelas', 'body' => 'Suasana kelas.'])
            ->assertSessionHasErrors('gallery_images');

        $this->post(route('manage.posts.store', 'gallery'), [
            'title' => 'Galeri kelas', 'body' => 'Suasana kelas.',
            'gallery_images' => [UploadedFile::fake()->image('kelas.jpg')],
        ])->assertRedirect(route('manage.posts.type', 'gallery'));
        $this->assertCount(1, Post::where('type', 'gallery')->firstOrFail()->gallery_paths);
    }

    public function test_existing_content_keeps_its_type_when_edited(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::create(['user_id' => $admin->id, 'type' => 'announcement', 'title' => 'Informasi lama', 'slug' => 'informasi-lama', 'body' => 'Isi lama', 'status' => 'draft']);

        $this->actingAs($admin)->get(route('manage.posts.edit', $post))->assertOk()->assertSee('Isi pengumuman')->assertDontSee('name="type"', false);
        $this->put(route('manage.posts.update', $post), ['type' => 'news', 'title' => 'Informasi baru', 'body' => 'Isi baru'])
            ->assertRedirect(route('manage.posts.type', 'announcement'));
        $this->assertSame('announcement', $post->fresh()->type);
        $this->get(route('manage.posts.type', 'announcement'))->assertSee('Informasi baru');
        $this->get(route('manage.posts.type', 'news'))->assertDontSee('Informasi baru');
    }
}
