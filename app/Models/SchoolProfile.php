<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
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
        return ['navigation_items' => 'array', 'hero_primary_enabled' => 'boolean', 'hero_secondary_enabled' => 'boolean'];
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
}
