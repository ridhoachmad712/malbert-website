@extends('layouts.site')
@section('title', 'Beranda')
@section('content')
<div class="home-page">
    <section class="home-hero hero-overlay-{{ $school->hero_overlay ?: 'normal' }} hero-style-{{ $school->hero_style ?: 'full' }}" aria-labelledby="hero-title">
        @if($school->hero_path)
            <div class="home-hero-background" aria-hidden="true"><img class="hero-position-{{ $school->hero_image_position ?: 'center' }}" src="{{ asset('storage/'.$school->hero_path) }}" alt="" fetchpriority="high"></div>
        @endif
        <div class="container home-hero-grid">
            <div class="home-hero-copy">
                <span class="eyebrow">{{ $school->name }}</span>
                <h1 id="hero-title">{{ $school->hero_title ?: 'Kabar sekolah,' }} <em>{{ $school->hero_highlight ?: 'dekat dengan keluarga.' }}</em></h1>
                <p>{{ $school->hero_description ?: 'Temukan pengumuman, agenda, profil guru, dan informasi penerimaan murid dari sekolah.' }}</p>
                <div class="home-hero-actions">
                    @if(($school->hero_primary_enabled ?? true) && $school->heroUrl('primary'))<a class="button" href="{{ $school->heroUrl('primary') }}" @if($school->hero_primary_target_type === 'external')target="_blank" rel="noopener noreferrer"@endif>{{ $school->hero_primary_label ?: 'Informasi penerimaan' }}</a>@endif
                    @if(($school->hero_secondary_enabled ?? true) && $school->heroUrl('secondary'))<a class="home-hero-secondary" href="{{ $school->heroUrl('secondary') }}" @if($school->hero_secondary_target_type === 'external')target="_blank" rel="noopener noreferrer"@endif>{{ $school->hero_secondary_label ?: 'Profil sekolah' }}</a>@endif
                </div>
            </div>
        </div>
        @if($school->hero_path && str_starts_with($school->hero_path, 'demo/'))<p class="home-hero-demo-note">Ilustrasi contoh · ganti dengan foto sekolah yang telah disetujui</p>@endif
    </section>

    <div class="container">
        <div class="home-sections">
        @if($school->homeSection('shortcuts')['enabled'])
        <nav class="home-shortcuts" aria-label="Akses informasi utama" style="order: {{ $school->homeSection('shortcuts')['order'] }}">
            <a href="{{ route('announcements') }}"><small>Informasi resmi</small><strong>Pengumuman</strong></a>
            <a href="{{ route('events') }}"><small>Jadwal kegiatan</small><strong>Agenda sekolah</strong></a>
            <a href="{{ route('contact') }}"><small>Hubungi kami</small><strong>Kontak dan lokasi</strong></a>
        </nav>
        @endif

        @if($school->homeSection('updates')['enabled'])
        <section class="home-section home-updates" aria-labelledby="updates-title" style="order: {{ $school->homeSection('updates')['order'] }}">
            <div class="section-heading"><div><span class="eyebrow">BERITA SEKOLAH</span><h2 id="updates-title">Kabar terbaru</h2></div><a class="text-link" href="{{ route('news') }}">Semua berita</a></div>
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
                        <div class="home-information-heading"><h3>Pengumuman</h3><a href="{{ route('announcements') }}" aria-label="Lihat semua pengumuman">Lihat semua</a></div>
                        @forelse($announcements as $item)
                            <a class="home-information-link" href="{{ $item->publicUrl() }}"><small>{{ $item->published_at?->translatedFormat('d M Y') }}</small><strong>{{ $item->title }}</strong></a>
                        @empty
                            <p>Belum ada pengumuman terbaru.</p>
                        @endforelse
                    </div>
                    <div class="home-information-block">
                        <div class="home-information-heading"><h3>Agenda mendatang</h3><a href="{{ route('events') }}" aria-label="Lihat semua agenda">Lihat semua</a></div>
                        @forelse($events as $item)
                            <a class="home-information-link" href="{{ $item->publicUrl() }}"><small>{{ $item->event_date?->translatedFormat('d M Y') }}</small><strong>{{ $item->title }}</strong></a>
                        @empty
                            <p>Agenda mendatang belum diumumkan.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </section>
        @endif

        @if($school->homeSection('programs')['enabled'])
        <section class="home-section home-programs" aria-labelledby="programs-title" style="order: {{ $school->homeSection('programs')['order'] }}">
            <div class="section-heading"><div><span class="eyebrow">KEGIATAN BELAJAR</span><h2 id="programs-title">Program dan lingkungan belajar</h2></div><a class="text-link" href="{{ route('programs') }}">Semua program</a></div>
            <div class="home-program-grid">
                @if($programs->isNotEmpty())
                    @php($program = $programs->first())
                    <article class="home-program-feature">
                        <a class="home-program-image" href="{{ $program->publicUrl() }}" aria-label="Baca {{ $program->title }}">
                            @if($program->cover_path)<img src="{{ asset('storage/'.$program->cover_path) }}" alt="{{ $program->cover_alt ?: '' }}" loading="lazy">@else<span class="image-placeholder">Foto program belum tersedia</span>@endif
                        </a>
                        <div><span class="eyebrow">PROGRAM SEKOLAH</span><h3><a href="{{ $program->publicUrl() }}">{{ $program->title }}</a></h3><p>{{ $program->summary ?: Str::limit($program->body, 150) }}</p><a class="text-link" href="{{ $program->publicUrl() }}">Baca program</a></div>
                    </article>
                @else
                    <div class="empty"><strong>Program akan tampil di sini.</strong><p>Rincian program ditampilkan setelah sekolah menerbitkannya.</p></div>
                @endif
                <div class="home-program-aside">
                    <a href="{{ route('facilities') }}"><span class="eyebrow">FASILITAS</span><strong>Ruang dan sarana sekolah</strong><span>Lihat fasilitas yang telah dipublikasikan.</span></a>
                    <a href="{{ route('achievements') }}"><span class="eyebrow">PRESTASI</span><strong>Prestasi warga sekolah</strong><span>Baca capaian yang telah diterbitkan sekolah.</span></a>
                </div>
            </div>
        </section>
        @endif

        @if($school->homeSection('principal')['enabled'] && $school->principal_greeting && $school->principal_name)
            <section class="home-section home-principal {{ $school->principal_photo_path ? 'has-principal-photo' : '' }}" aria-labelledby="principal-title" style="order: {{ $school->homeSection('principal')['order'] }}">
                <div class="home-principal-copy">
                    <span class="eyebrow">DARI KEPALA SEKOLAH</span>
                    <h2 id="principal-title">Sambutan kepala sekolah</h2>
                    <p>{{ Str::limit($school->principal_greeting, 300) }}</p>
                    @unless($school->principal_photo_path)<strong>{{ $school->principal_name }}</strong>@endunless
                    <a class="text-link" href="{{ route('profile') }}">Baca profil sekolah</a>
                </div>
                @if($school->principal_photo_path)
                    <figure class="home-principal-portrait">
                        <img src="{{ asset('storage/'.$school->principal_photo_path) }}" alt="Foto {{ $school->principal_name }}" loading="lazy">
                        <figcaption><span>Kepala Sekolah</span><strong>{{ $school->principal_name }}</strong></figcaption>
                    </figure>
                @endif
            </section>
        @endif

        @if($school->homeSection('teachers')['enabled'] && $teachers->isNotEmpty())
            <section class="home-section home-teachers" aria-labelledby="teachers-title" style="order: {{ $school->homeSection('teachers')['order'] }}"><div class="section-heading"><div><span class="eyebrow">GURU DAN TENAGA KEPENDIDIKAN</span><h2 id="teachers-title">Orang-orang di sekolah</h2></div><a class="text-link" href="{{ route('teachers') }}">Semua profil guru</a></div><div class="teacher-grid">@foreach($teachers as $teacher)<x-teacher-card :teacher="$teacher" />@endforeach</div></section>
        @endif

        @if($school->homeSection('contact')['enabled'])
        <section class="home-section contact-band" style="order: {{ $school->homeSection('contact')['order'] }}"><div><span class="eyebrow">HUBUNGI SEKOLAH</span><h2>Alamat dan kontak</h2><p>{{ $school->address ?: 'Alamat resmi akan dilengkapi pengelola sekolah.' }}</p></div><a class="button button-light" href="{{ route('contact') }}">Lihat kontak sekolah</a></section>
        @endif
        </div>
    </div>
</div>
@endsection
