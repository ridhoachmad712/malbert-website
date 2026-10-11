<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $school->name) · {{ $school->name }}</title>
    <meta name="description" content="@yield('description', $school->tagline ?: 'Informasi resmi sekolah, berita, pengumuman, dan profil guru.')">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
    @auth<meta name="robots" content="noindex" />@endauth
</head>
<body>
<a class="skip" href="#main">Lewati ke isi utama</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Beranda {{ $school->name }}">
            @if($school->logo_path)<img src="{{ asset('storage/'.$school->logo_path) }}" alt="" class="brand-logo">@else<span class="brand-mark" aria-hidden="true">✳</span>@endif
            <span><strong>{{ $school->name }}</strong><small>Website sekolah</small></span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu <span aria-hidden="true">☰</span></button>
        <nav id="site-nav" class="site-nav" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('profile') }}" @class(['active' => request()->routeIs('profile')])>Profil</a>
            <a href="{{ route('teachers') }}" @class(['active' => request()->routeIs('teachers', 'teacher')])>Guru</a>
            <a href="{{ route('news') }}" @class(['active' => request()->routeIs('news')])>Berita</a>
            <a href="{{ route('admissions') }}" @class(['active' => request()->routeIs('admissions')])>Penerimaan</a>
            <details class="nav-more"><summary>Jelajahi <span aria-hidden="true">⌄</span></summary><div>
                <a href="{{ route('programs') }}">Program</a><a href="{{ route('facilities') }}">Fasilitas</a>
                <a href="{{ route('events') }}">Agenda</a><a href="{{ route('achievements') }}">Prestasi</a>
                <a href="{{ route('announcements') }}">Pengumuman</a><a href="{{ route('gallery') }}">Galeri</a>
                <a href="{{ route('faq') }}">Tanya jawab</a>
            </div></details>
            <a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact')])>Kontak</a>
            @if($school->aksikita_url)<a href="{{ $school->aksikita_url }}" target="_blank" rel="noopener">AksiKita ↗</a>@endif
        </nav>
    </div>
</header>
<main id="main">@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div><span class="eyebrow">{{ $school->name }}</span><p>{{ $school->tagline ?: 'Informasi sekolah yang mudah dijangkau.' }}</p></div>
        <div><strong>Jelajahi</strong><a href="{{ route('profile') }}">Profil sekolah</a><a href="{{ route('programs') }}">Program & fasilitas</a><a href="{{ route('events') }}">Agenda</a><a href="{{ route('admissions') }}">Penerimaan murid</a><a href="{{ route('faq') }}">Tanya jawab</a></div>
        <div><strong>Hubungi</strong><p>{{ $school->address ?: 'Alamat resmi belum dilengkapi.' }}</p>@if($school->email)<a href="mailto:{{ $school->email }}">{{ $school->email }}</a>@endif</div>
    </div>
    <div class="container footer-bottom"><small>© {{ date('Y') }} {{ $school->name }}</small><a href="{{ route('login') }}">Masuk pengelola</a></div>
</footer>
<script src="{{ asset('js/school.js') }}" defer></script>
</body>
</html>
