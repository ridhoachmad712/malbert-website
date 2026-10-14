<?php

namespace Tests\Feature;

use App\Models\SiteMenuItem;
use App\Models\SitePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_a_page_and_link_it_from_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('manage.pages'))->assertOk();
        $this->actingAs($admin)->get(route('manage.pages.create'))->assertOk();
        $pageData = [
            'title' => 'Kurikulum Sekolah', 'slug' => 'kurikulum-sekolah', 'intro' => 'Belajar dengan gembira',
            'body' => 'Isi halaman kurikulum.', 'layout' => 'wide', 'is_published' => 0,
        ];
        $this->actingAs($admin)->post(route('manage.pages.store'), $pageData)->assertRedirect()->assertSessionHasNoErrors();
        $page = SitePage::firstOrFail();
        $this->actingAs($admin)->get(route('manage.pages.edit', $page))->assertOk();
        $this->get(route('site-page', $page->slug))->assertNotFound();
        $this->actingAs($admin)->get(route('manage.pages.preview', $page))->assertOk()->assertSee('Isi halaman kurikulum.');

        $pageData['is_published'] = 1;
        $this->actingAs($admin)->put(route('manage.pages.update', $page), $pageData)->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($admin)->get(route('manage.menu.create'))->assertOk()->assertSee('Kurikulum Sekolah');
        $this->actingAs($admin)->post(route('manage.menu.store'), [
            'label' => 'Kurikulum', 'target_type' => 'page', 'site_page_id' => $page->id,
            'location' => 'more', 'sort_order' => 15, 'is_enabled' => 1,
            'show_in_footer' => 1, 'open_new_tab' => 0,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($admin)->get(route('manage.menu'))->assertOk()->assertSee('Kurikulum');

        $this->get('/')->assertOk()->assertSee('Kurikulum')->assertSee(route('site-page', $page->slug));
        $this->get(route('site-page', $page->slug))->assertOk()->assertSee('Isi halaman kurikulum.');
        $this->get(route('sitemap'))->assertSee('/halaman/kurikulum-sekolah');
    }

    public function test_external_menu_requires_https_and_admin_access(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $this->actingAs($writer)->get(route('manage.menu'))->assertForbidden();
        $this->actingAs($writer)->get(route('manage.pages'))->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'label' => 'Perpustakaan', 'target_type' => 'external',
            'external_url' => 'https://perpustakaan.example.sch.id', 'location' => 'more',
            'sort_order' => 20, 'is_enabled' => 1, 'show_in_footer' => 1, 'open_new_tab' => 1,
        ];
        $this->actingAs($admin)->post(route('manage.menu.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->get('/')->assertSee('https://perpustakaan.example.sch.id')->assertSee('rel="noopener noreferrer"', false);

        $payload['external_url'] = 'javascript:alert(1)';
        $this->actingAs($admin)->post(route('manage.menu.store'), $payload)->assertSessionHasErrors('external_url');
        $this->assertSame(1, SiteMenuItem::where('label', 'Perpustakaan')->count());
    }

    public function test_draft_page_and_its_menu_are_hidden_publicly(): void
    {
        $page = SitePage::create(['title' => 'Draf Rahasia', 'slug' => 'draf-rahasia', 'body' => 'Jangan tampilkan', 'layout' => 'standard', 'is_published' => false]);
        SiteMenuItem::create(['label' => 'Draf Rahasia', 'target_type' => 'page', 'site_page_id' => $page->id, 'location' => 'more', 'sort_order' => 10, 'is_enabled' => true, 'show_in_footer' => true, 'open_new_tab' => false]);

        $this->get('/')->assertOk()->assertDontSee('Draf Rahasia');
        $this->get(route('site-page', $page->slug))->assertNotFound();
        $this->get(route('sitemap'))->assertDontSee('/halaman/draf-rahasia');
    }
}
