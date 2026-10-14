@extends('layouts.manage')
@section('title', 'Halaman')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">WEBSITE</span><h1>Halaman</h1><p>Buat halaman informasi yang dapat ditautkan dari menu dan tombol.</p></div><a class="button" href="{{ route('manage.pages.create') }}">Buat halaman</a></header>
<section class="panel builder-list" aria-label="Daftar halaman">
    @forelse($pages as $page)
        <div class="builder-list-row"><div><strong>{{ $page->title }}</strong><small>/halaman/{{ $page->slug }} · {{ $page->is_published ? 'Terbit' : 'Draf' }}</small></div><div class="builder-list-actions"><a class="button outline" href="{{ route('manage.pages.preview', $page) }}" target="_blank" rel="noopener">Pratinjau</a><a class="button outline" href="{{ route('manage.pages.edit', $page) }}">Ubah</a></div></div>
    @empty
        <div class="empty"><strong>Belum ada halaman tambahan.</strong><p>Halaman profil, guru, berita, dan informasi bawaan tetap tersedia.</p></div>
    @endforelse
</section>
@endsection
