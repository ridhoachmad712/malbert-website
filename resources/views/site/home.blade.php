@extends('layouts.site')
@section('title', 'Beranda')
@section('content')
<div class="container">
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy"><span class="eyebrow">SEKOLAH · BELAJAR · BERTUMBUH</span><h1 id="hero-title">Tempat belajar, <em>bertumbuh bersama.</em></h1><p>{{ $school->tagline ?: 'Kenali sekolah, temukan informasi terbaru, dan ikuti cerita kegiatan kami di sini.' }}</p><a class="button" href="{{ route('profile') }}">Kenali sekolah <span aria-hidden="true">↗</span></a></div>
        <div class="hero-visual">@if($school->hero_path)<img src="{{ asset('storage/'.$school->hero_path) }}" alt="Foto lingkungan {{ $school->name }}" fetchpriority="high">@else<div class="hero-placeholder"><span aria-hidden="true">✳</span><p>Foto sekolah akan tampil di sini setelah ditambahkan pengelola.</p></div>@endif</div>
    </section>
    <nav class="quick-links" aria-label="Jelajahi informasi"><a href="{{ route('announcements') }}"><small>01 / Informasi</small><strong>Pengumuman sekolah</strong><span aria-hidden="true">↗</span></a><a href="{{ route('teachers') }}"><small>02 / Pendidik</small><strong>Kenali guru kami</strong><span aria-hidden="true">↗</span></a><a href="{{ route('contact') }}"><small>03 / Layanan</small><strong>Temukan dan hubungi</strong><span aria-hidden="true">↗</span></a></nav>
    @if($announcements->isNotEmpty())<section class="section news-alert"><div><span class="eyebrow">KABAR PENTING</span><h2>Pengumuman terbaru</h2></div><div>@foreach($announcements as $item)<a href="{{ $item->publicUrl() }}"><small>{{ $item->published_at?->translatedFormat('d M Y') }}</small><strong>{{ $item->title }}</strong><span aria-hidden="true">↗</span></a>@endforeach</div></section>@endif
    <section class="section"><div class="section-heading"><div><span class="eyebrow">CERITA SEKOLAH</span><h2>Kegiatan dan kabar terbaru</h2></div><a class="text-link" href="{{ route('news') }}">Semua berita <span aria-hidden="true">↗</span></a></div>
        @if($news->isEmpty())<div class="empty"><strong>Berita akan hadir di sini.</strong><p>Pengelola sekolah sedang menyiapkan cerita kegiatan yang dapat dibagikan.</p></div>@else<div class="card-grid">@foreach($news as $post)<x-post-card :post="$post" />@endforeach</div>@endif
    </section>
    @if($school->programs)<section class="section feature-band"><span class="eyebrow">BELAJAR DI SEKOLAH</span><h2>Program sekolah</h2><p>{{ Str::limit($school->programs, 260) }}</p><a class="text-link" href="{{ route('profile') }}">Lihat profil sekolah <span aria-hidden="true">↗</span></a></section>@endif
    @if($teachers->isNotEmpty())<section class="section"><div class="section-heading"><div><span class="eyebrow">WAJAH SEKOLAH</span><h2>Kenali pendidik kami</h2></div><a class="text-link" href="{{ route('teachers') }}">Lihat semua guru <span aria-hidden="true">↗</span></a></div><div class="teacher-grid">@foreach($teachers as $teacher)<x-teacher-card :teacher="$teacher" />@endforeach</div></section>@endif
    <section class="section contact-band"><div><span class="eyebrow">KUNJUNGI KAMI</span><h2>Temukan sekolah kami</h2><p>{{ $school->address ?: 'Alamat resmi akan dilengkapi pengelola sekolah.' }}</p></div><a class="button button-light" href="{{ route('contact') }}">Lihat kontak dan lokasi <span aria-hidden="true">↗</span></a></section>
</div>
@endsection
