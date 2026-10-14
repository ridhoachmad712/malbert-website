<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $school->name) · {{ $school->name }}</title>
    <meta name="description" content="@yield('description', $school->tagline ?: 'Informasi resmi sekolah, berita, pengumuman, dan profil guru.')">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/school.css') }}">
    <link rel="stylesheet" href="{{ asset('css/school-public.css') }}">
    <style>:root{--blue:{{ $school->theme_primary_color ?: '#2459d8' }};--blue-deep:color-mix(in srgb,var(--blue) 76%,#061f48);--green:{{ $school->theme_accent_color ?: '#1b7661' }}}</style>
    @auth<meta name="robots" content="noindex" />@endauth
</head>
<body class="theme-font-{{ $school->theme_font ?: 'jakarta' }} theme-header-{{ $school->header_style ?: 'standard' }} theme-card-{{ $school->card_style ?: 'rounded' }}">
@php
    $visibleNavigation = \App\Models\SiteMenuItem::with('page')->where('is_enabled', true)->orderBy('sort_order')->get()->filter(fn ($item): bool => (bool) $item->url());
    $navigationByLocation = $visibleNavigation->groupBy('location');
@endphp
<a class="skip" href="#main">Lewati ke isi utama</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Beranda {{ $school->name }}">
            @if($school->logo_path)<img src="{{ asset('storage/'.$school->logo_path) }}" alt="" class="brand-logo">@else<span class="brand-mark" aria-hidden="true">SD</span>@endif
            <span><strong>{{ $school->name }}</strong><small>Website sekolah</small></span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu <span aria-hidden="true">☰</span></button>
        <nav id="site-nav" class="site-nav" aria-label="Navigasi utama">
            @foreach($navigationByLocation->get('primary', collect()) as $item)
                <a href="{{ $item->url() }}" @class(['active' => $item->isActive()]) @if($item->open_new_tab)target="_blank" rel="noopener noreferrer"@endif>{{ $item->label }}</a>
            @endforeach
            @if($navigationByLocation->has('more'))
            <details @class(['nav-more', 'active' => $navigationByLocation->get('more')->contains(fn ($item): bool => $item->isActive())])>
                <summary>{{ $school->navigation_more_label ?: 'Informasi' }}</summary>
                <div class="nav-more-panel">
                    @foreach($navigationByLocation->get('more') as $item)
                        <a href="{{ $item->url() }}" @class(['active' => $item->isActive()]) @if($item->open_new_tab)target="_blank" rel="noopener noreferrer"@endif>{{ $item->label }}</a>
                    @endforeach
                </div>
            </details>
            @endif
            @foreach($navigationByLocation->get('cta', collect()) as $item)
                <a href="{{ $item->url() }}" @class(['nav-cta', 'active' => $item->isActive()]) @if($item->open_new_tab)target="_blank" rel="noopener noreferrer"@endif>{{ $item->label }}</a>
            @endforeach
        </nav>
    </div>
</header>
<main id="main">@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div><span class="eyebrow">{{ $school->name }}</span><p>{{ $school->tagline ?: 'Informasi sekolah yang mudah dijangkau.' }}</p></div>
        <div><strong>Jelajahi</strong>@foreach($visibleNavigation->filter(fn ($item): bool => $item->show_in_footer)->take(8) as $item)<a href="{{ $item->url() }}" @if($item->open_new_tab)target="_blank" rel="noopener noreferrer"@endif>{{ $item->label }}</a>@endforeach</div>
        <div><strong>Hubungi</strong><p>{{ $school->address ?: 'Alamat resmi belum dilengkapi.' }}</p>@if($school->email)<a href="mailto:{{ $school->email }}">{{ $school->email }}</a>@endif</div>
    </div>
    <div class="container footer-bottom"><small>© {{ date('Y') }} {{ $school->name }}</small><a href="{{ route('login') }}">Masuk pengelola</a></div>
</footer>
<script src="{{ asset('js/school.js') }}" defer></script>
</body>
</html>
