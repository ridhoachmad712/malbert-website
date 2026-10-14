<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;

class SiteMenuItem extends Model
{
    public const ROUTES = [
        'home' => 'Beranda', 'profile' => 'Profil sekolah', 'teachers' => 'Guru',
        'news' => 'Berita', 'announcements' => 'Pengumuman', 'gallery' => 'Galeri',
        'programs' => 'Program', 'facilities' => 'Fasilitas', 'events' => 'Agenda',
        'achievements' => 'Prestasi', 'admissions' => 'Penerimaan murid',
        'faq' => 'Tanya jawab', 'contact' => 'Kontak',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'show_in_footer' => 'boolean', 'open_new_tab' => 'boolean'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(SitePage::class, 'site_page_id');
    }

    public function url(): ?string
    {
        return match ($this->target_type) {
            'route' => isset(self::ROUTES[$this->target_route]) && Route::has($this->target_route) ? route($this->target_route) : null,
            'page' => $this->page?->is_published ? route('site-page', $this->page->slug) : null,
            'external' => filter_var($this->external_url, FILTER_VALIDATE_URL) && str_starts_with($this->external_url, 'https://') ? $this->external_url : null,
            default => null,
        };
    }

    public function isActive(): bool
    {
        return match ($this->target_type) {
            'route' => request()->routeIs($this->target_route) || ($this->target_route === 'teachers' && request()->routeIs('teacher')),
            'page' => request()->routeIs('site-page') && request()->route('sitePage')?->is($this->page),
            default => false,
        };
    }
}
