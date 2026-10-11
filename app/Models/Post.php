<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'gallery_paths' => 'array', 'published_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function scopePublic($query)
    {
        return $query->where('status', 'published')->where('published_at', '<=', now())
            ->where(fn ($items) => $items->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function publicUrl(): string
    {
        return route('post', ['type' => ['news' => 'berita', 'announcement' => 'pengumuman', 'gallery' => 'galeri'][$this->type], 'post' => $this->slug]);
    }
}
