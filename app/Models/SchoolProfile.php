<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class SchoolProfile extends Model
{
    public const HOME_SECTIONS = [
        'shortcuts' => ['label' => 'Pintasan', 'order' => 10],
        'principal' => ['label' => 'Sambutan kepala sekolah', 'order' => 11],
        'updates' => ['label' => 'Berita, pengumuman, agenda', 'order' => 20],
        'programs' => ['label' => 'Program dan fasilitas', 'order' => 30],
        'teachers' => ['label' => 'Profil guru', 'order' => 50],
        'contact' => ['label' => 'Kontak sekolah', 'order' => 60],
    ];

    public const NAVIGATION_DEFAULTS = [
        'home' => ['label' => 'Beranda', 'location' => 'primary', 'order' => 10],
        'profile' => ['label' => 'Profil', 'location' => 'primary', 'order' => 20],
        'teachers' => ['label' => 'Guru', 'location' => 'primary', 'order' => 30],
        'news' => ['label' => 'Berita', 'location' => 'primary', 'order' => 40],
        'programs' => ['label' => 'Program', 'location' => 'more', 'order' => 10],
        'facilities' => ['label' => 'Fasilitas', 'location' => 'more', 'order' => 20],
        'achievements' => ['label' => 'Prestasi', 'location' => 'more', 'order' => 30],
        'gallery' => ['label' => 'Galeri', 'location' => 'more', 'order' => 40],
        'announcements' => ['label' => 'Pengumuman', 'location' => 'more', 'order' => 50],
        'events' => ['label' => 'Agenda', 'location' => 'more', 'order' => 60],
        'faq' => ['label' => 'Tanya jawab', 'location' => 'more', 'order' => 70],
        'contact' => ['label' => 'Kontak', 'location' => 'more', 'order' => 80],
        'aksikita' => ['label' => 'AksiKita', 'location' => 'primary', 'order' => 50],
        'admissions' => ['label' => 'Penerimaan murid', 'location' => 'cta', 'order' => 10],
    ];

    public const HERO_LINKS = [
        'admissions' => 'Penerimaan murid',
        'profile' => 'Profil sekolah',
        'teachers' => 'Guru',
        'news' => 'Berita',
        'announcements' => 'Pengumuman',
        'events' => 'Agenda',
        'programs' => 'Program',
        'facilities' => 'Fasilitas',
        'achievements' => 'Prestasi',
        'gallery' => 'Galeri',
        'faq' => 'Tanya jawab',
        'contact' => 'Kontak',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['navigation_items' => 'array', 'home_sections' => 'array', 'hero_primary_enabled' => 'boolean', 'hero_secondary_enabled' => 'boolean'];
    }

    public static function current(): self
    {
        return self::firstOrCreate([], ['name' => 'Nama sekolah belum diisi']);
    }

    public function navigationItems(): array
    {
        $saved = $this->navigation_items ?? [];
        $items = [];

        foreach (self::NAVIGATION_DEFAULTS as $key => $defaults) {
            $items[$key] = array_merge($defaults, ['enabled' => true], $saved[$key] ?? []);
            $items[$key]['key'] = $key;
        }

        $locationOrder = ['primary' => 0, 'more' => 1, 'cta' => 2];
        uasort($items, fn (array $first, array $second): int => ($locationOrder[$first['location']] <=> $locationOrder[$second['location']]) ?: ($first['order'] <=> $second['order']));

        return $items;
    }

    public function homeSection(string $key): array
    {
        return array_merge(['enabled' => true], self::HOME_SECTIONS[$key], $this->home_sections[$key] ?? []);
    }

    public function heroUrl(string $position): ?string
    {
        $type = $this->{'hero_'.$position.'_target_type'} ?: 'route';

        if ($type === 'page') {
            $page = SitePage::whereKey($this->{'hero_'.$position.'_page_id'})->where('is_published', true)->first();

            return $page ? route('site-page', $page->slug) : null;
        }

        $routeName = $this->{'hero_'.$position.'_route'} ?: ($position === 'primary' ? 'admissions' : 'profile');
        $externalUrl = $this->{'hero_'.$position.'_external_url'};

        return match ($type) {
            'route' => isset(SiteMenuItem::ROUTES[$routeName]) && Route::has($routeName) ? route($routeName) : null,
            'external' => filter_var($externalUrl, FILTER_VALIDATE_URL) && str_starts_with($externalUrl, 'https://') ? $externalUrl : null,
            default => null,
        };
    }
}
