@extends('layouts.site')
@section('title', 'Beranda')
@section('content')
<div class="home-page">
    <section class="home-hero" aria-labelledby="hero-title">
        <div class="container home-hero-grid">
            <div class="home-hero-copy">
                <span class="eyebrow">SELAMAT DATANG DI WEBSITE SEKOLAH</span>
                <h1 id="hero-title">Tempat belajar, <em>bertumbuh bersama.</em></h1>
                <p>{{ $school->tagline ?: 'Kenali sekolah, temukan informasi terbaru, dan ikuti cerita kegiatan kami di sini.' }}</p>
                <div class="home-hero-actions">
                    <a class="button button-light" href="{{ route('admissions') }}">Informasi penerimaan <span aria-hidden="true">↗</span></a>
                    <a class="home-hero-secondary" href="{{ route('profile') }}">Kenali sekolah <span aria-hidden="true">→</span></a>
                </div>
            </div>
            <figure class="home-hero-media">
                @if($school->hero_path)
                    <img src="{{ asset('storage/'.$school->hero_path) }}" alt="{{ str_starts_with($school->hero_path, 'demo/') ? 'Ilustrasi sekolah untuk pratinjau website' : 'Lingkungan '.$school->name }}" fetchpriority="high">
                    @if(str_starts_with($school->hero_path, 'demo/'))<figcaption>Ilustrasi contoh · ganti dengan foto sekolah yang telah disetujui</figcaption>@endif
                @else
                    <div class="home-hero-placeholder"><span aria-hidden="true">✳</span><p>Foto sekolah akan tampil di sini setelah ditambahkan pengelola.</p></div>
                @endif
            </figure>
        </div>
    </section>

    <div class="container">
        <nav class="home-shortcuts" aria-label="Akses informasi utama">
            <a href="{{ route('announcements') }}"><span class="shortcut-icon" aria-hidden="true">01</span><span><small>Informasi resmi</small><strong>Pengumuman</strong></span><span aria-hidden="true">↗</span></a>
            <a href="{{ route('events') }}"><span class="shortcut-icon" aria-hidden="true">02</span><span><small>Jadwal kegiatan</small><strong>Agenda sekolah</strong></span><span aria-hidden="true">↗</span></a>
            <a href="{{ route('contact') }}"><span class="shortcut-icon" aria-hidden="true">03</span><span><small>Layanan sekolah</small><strong>Kontak & lokasi</strong></span><span aria-hidden="true">↗</span></a>
        </nav>

        <section class="home-section home-updates" aria-labelledby="updates-title">
            <div class="section-heading"><div><span class="eyebrow">TERBARU DARI SEKOLAH</span><h2 id="updates-title">Kabar yang perlu diketahui</h2></div><a class="text-link" href="{{ route('news') }}">Lihat semua berita ↗</a></div>
            <div class="home-updates-grid">
                <div class="home-news">
                    @forelse($news as $post)
                        <x-post-card :post="$post" />
                    @empty
                        <div class="empty"><strong>Berita akan hadir di sini.</strong><p>Pengelola sedang menyiapkan kabar sekolah yang dapat dibagikan.</p></div>
                    @endforelse
                </div>
                <aside class="home-information" aria-label="Pengumuman dan agenda">
                    <div class="home-information-block">
                        <div class="home-information-heading"><h3>Pengumuman</h3><a href="{{ route('announcements') }}" aria-label="Lihat semua pengumuman">Semua ↗</a></div>
                        @forelse($announcements as $item)
                            <a class="home-information-link" href="{{ $item->publicUrl() }}"><small>{{ $item->published_at?->translatedFormat('d M Y') }}</small><strong>{{ $item->title }}</strong></a>
                        @empty
                            <p>Belum ada pengumuman terbaru.</p>
                        @endforelse
                    </div>
                    <div class="home-information-block">
                        <div class="home-information-heading"><h3>Agenda mendatang</h3><a href="{{ route('events') }}" aria-label="Lihat semua agenda">Semua ↗</a></div>
                        @forelse($events as $item)
                            <a class="home-information-link" href="{{ $item->publicUrl() }}"><small>{{ $item->event_date?->translatedFormat('d M Y') }}</small><strong>{{ $item->title }}</strong></a>
                        @empty
                            <p>Agenda mendatang belum diumumkan.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </section>

        <section class="home-section home-programs" aria-labelledby="programs-title">
            <div class="section-heading"><div><span class="eyebrow">BELAJAR DI SEKOLAH</span><h2 id="programs-title">Program & lingkungan belajar</h2></div><a class="text-link" href="{{ route('programs') }}">Lihat semua program ↗</a></div>
            <div class="home-program-grid">
                @if($programs->isNotEmpty())
                    @php($program = $programs->first())
                    <article class="home-program-feature">
                        <a class="home-program-image" href="{{ $program->publicUrl() }}" aria-label="Baca {{ $program->title }}">
                            @if($program->cover_path)<img src="{{ asset('storage/'.$program->cover_path) }}" alt="{{ $program->cover_alt ?: '' }}" loading="lazy">@else<span aria-hidden="true">✳</span>@endif
                        </a>
                        <div><span class="eyebrow">PROGRAM SEKOLAH</span><h3><a href="{{ $program->publicUrl() }}">{{ $program->title }}</a></h3><p>{{ $program->summary ?: Str::limit($program->body, 150) }}</p><a class="text-link" href="{{ $program->publicUrl() }}">Lihat program ↗</a></div>
                    </article>
                @else
                    <div class="empty"><strong>Program akan tampil di sini.</strong><p>Rincian program ditampilkan setelah sekolah menerbitkannya.</p></div>
                @endif
                <div class="home-program-aside">
                    <a href="{{ route('facilities') }}"><span class="eyebrow">FASILITAS</span><strong>Jelajahi ruang belajar</strong><span>Lihat sarana sekolah yang telah dipublikasikan.</span><b aria-hidden="true">↗</b></a>
                    <a href="{{ route('achievements') }}"><span class="eyebrow">PRESTASI</span><strong>Apresiasi capaian</strong><span>Baca capaian yang telah diperiksa sekolah.</span><b aria-hidden="true">↗</b></a>
                </div>
            </div>
        </section>

        @if($school->principal_greeting && $school->principal_name)
            <section class="home-section home-principal" aria-labelledby="principal-title">
                <div><span class="eyebrow">DARI KEPALA SEKOLAH</span><h2 id="principal-title">Sambutan kepala sekolah</h2><p>{{ Str::limit($school->principal_greeting, 300) }}</p><strong>{{ $school->principal_name }}</strong><a class="text-link" href="{{ route('profile') }}">Baca profil sekolah ↗</a></div>
                <span class="home-principal-mark" aria-hidden="true">“</span>
            </section>
        @endif

        @if($teachers->isNotEmpty())
            <section class="home-section home-teachers" aria-labelledby="teachers-title"><div class="section-heading"><div><span class="eyebrow">ORANG-ORANG DI SEKOLAH</span><h2 id="teachers-title">Kenali pendidik kami</h2></div><a class="text-link" href="{{ route('teachers') }}">Lihat semua guru ↗</a></div><div class="teacher-grid">@foreach($teachers as $teacher)<x-teacher-card :teacher="$teacher" />@endforeach</div></section>
        @endif

        <section class="home-section contact-band"><div><span class="eyebrow">HUBUNGI SEKOLAH</span><h2>Temukan kami</h2><p>{{ $school->address ?: 'Alamat resmi akan dilengkapi pengelola sekolah.' }}</p></div><a class="button button-light" href="{{ route('contact') }}">Kontak dan lokasi ↗</a></section>
    </div>
</div>
@endsection
