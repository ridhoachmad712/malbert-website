@extends('layouts.manage')
@section('title', 'Tampilan website')
@section('content')
<header class="manage-title title-with-action">
    <div><span class="eyebrow">WEBSITE</span><h1>Susunan & tampilan</h1><p>Atur identitas visual dan susunan beranda. Menu dan tautan dikelola pada halaman tersendiri.</p></div>
    <a class="button outline" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat beranda</a>
</header>
<nav class="manage-section-nav" aria-label="Bagian pengaturan tampilan">
    <a href="#identity-title">Logo</a>
    <a href="#hero-settings-title">Hero & tombol</a>
    <a href="#section-settings-title">Bagian beranda</a>
    <a href="#theme-settings-title">Warna & bentuk</a>
</nav>
<form id="appearance-form" class="appearance-form" method="post" enctype="multipart/form-data" action="{{ route('manage.appearance.update') }}">
    @csrf @method('PUT')
    <section class="panel appearance-panel" aria-labelledby="identity-title">
        <div class="appearance-heading"><span class="eyebrow">01 / IDENTITAS</span><h2 id="identity-title">Logo sekolah</h2><p>Logo tampil di bagian atas website. Gunakan berkas dengan latar transparan agar terlihat rapi.</p></div>
        <div class="appearance-media-row">
            <div class="appearance-preview appearance-logo-preview">
                @if($school->logo_path)<img src="{{ asset('storage/'.$school->logo_path) }}" alt="Logo sekolah saat ini">@else<span>Belum ada logo</span>@endif
            </div>
            <div class="appearance-media-controls">
                <label>Ganti logo<input type="file" name="logo" accept="image/png,image/jpeg,image/webp"><small>PNG, JPG, atau WebP. Maksimal 2 MB.</small></label>
                @if($school->logo_path)<label class="check"><input type="checkbox" name="remove_logo" value="1"> Hapus logo saat ini</label>@endif
            </div>
        </div>
    </section>

    <section class="panel appearance-panel" aria-labelledby="hero-settings-title">
        <div class="appearance-heading"><span class="eyebrow">02 / BERANDA</span><h2 id="hero-settings-title">Hero beranda</h2><p>Gambar akan menjadi latar penuh. Lapisan gelap membantu menjaga teks tetap terbaca.</p></div>
        <div class="appearance-hero-preview">
            @if($school->hero_path)<img src="{{ asset('storage/'.$school->hero_path) }}" alt="Gambar hero saat ini">@else<span>Belum ada gambar latar</span>@endif
        </div>
        <div class="appearance-fields">
            <label class="wide">Ganti gambar latar<input type="file" name="hero" accept="image/png,image/jpeg,image/webp"><small>Disarankan gambar mendatar minimal 1600 × 900 piksel. Maksimal 6 MB.</small></label>
            @if($school->hero_path)<label class="check wide"><input type="checkbox" name="remove_hero" value="1"> Hapus gambar latar saat ini</label>@endif
            <label>Baris judul pertama<input name="hero_title" value="{{ old('hero_title', $school->hero_title) }}" maxlength="120" placeholder="Kabar sekolah,"></label>
            <label>Baris judul kedua<input name="hero_highlight" value="{{ old('hero_highlight', $school->hero_highlight) }}" maxlength="120" placeholder="dekat dengan keluarga."></label>
            <label class="wide">Teks penjelas<textarea name="hero_description" rows="3" maxlength="350" placeholder="Temukan pengumuman, agenda, profil guru, dan informasi penerimaan murid dari sekolah.">{{ old('hero_description', $school->hero_description) }}</textarea></label>
            <label>Posisi gambar<select name="hero_image_position">@foreach(['left'=>'Kiri','center'=>'Tengah','right'=>'Kanan'] as $value => $label)<option value="{{ $value }}" @selected(old('hero_image_position', $school->hero_image_position ?: 'center') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>Kekuatan lapisan gelap<select name="hero_overlay">@foreach(['soft'=>'Ringan','normal'=>'Sedang','strong'=>'Kuat'] as $value => $label)<option value="{{ $value }}" @selected(old('hero_overlay', $school->hero_overlay ?: 'normal') === $value)>{{ $label }}</option>@endforeach</select></label>
            <div class="wide appearance-subheading"><h3>Tombol pada hero</h3><p>Pilih tujuan dan tentukan tombol mana yang tampil.</p></div>
            <label class="check wide"><input type="hidden" name="hero_primary_enabled" value="0"><input type="checkbox" name="hero_primary_enabled" value="1" @checked(old('hero_primary_enabled', $school->hero_primary_enabled ?? true))> Tampilkan tombol utama</label>
            <label>Teks tombol utama<input name="hero_primary_label" value="{{ old('hero_primary_label', $school->hero_primary_label) }}" maxlength="40" placeholder="Informasi penerimaan"></label>
            <label data-target-field="route">Tujuan tombol utama<select name="hero_primary_route">@foreach(\App\Models\SchoolProfile::HERO_LINKS as $route => $label)<option value="{{ $route }}" @selected(old('hero_primary_route', $school->hero_primary_route ?: 'admissions') === $route)>{{ $label }}</option>@endforeach</select></label>
            <label>Jenis tujuan utama<select name="hero_primary_target_type" data-target-type>@foreach(['route'=>'Halaman bawaan','page'=>'Halaman buatan admin','external'=>'URL eksternal'] as $value => $label)<option value="{{ $value }}" @selected(old('hero_primary_target_type', $school->hero_primary_target_type ?: 'route') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label data-target-field="page">Halaman buatan admin<select name="hero_primary_page_id"><option value="">Pilih halaman terbit</option>@foreach(\App\Models\SitePage::where('is_published', true)->orderBy('title')->get() as $page)<option value="{{ $page->id }}" @selected((string) old('hero_primary_page_id', $school->hero_primary_page_id) === (string) $page->id)>{{ $page->title }}</option>@endforeach</select></label>
            <label class="wide" data-target-field="external">URL eksternal<input name="hero_primary_external_url" type="url" value="{{ old('hero_primary_external_url', $school->hero_primary_external_url) }}" placeholder="https://contoh.sch.id" maxlength="500"></label>
            <label class="check wide"><input type="hidden" name="hero_secondary_enabled" value="0"><input type="checkbox" name="hero_secondary_enabled" value="1" @checked(old('hero_secondary_enabled', $school->hero_secondary_enabled ?? true))> Tampilkan tombol kedua</label>
            <label>Teks tombol kedua<input name="hero_secondary_label" value="{{ old('hero_secondary_label', $school->hero_secondary_label) }}" maxlength="40" placeholder="Profil sekolah"></label>
            <label data-target-field="route">Tujuan tombol kedua<select name="hero_secondary_route">@foreach(\App\Models\SchoolProfile::HERO_LINKS as $route => $label)<option value="{{ $route }}" @selected(old('hero_secondary_route', $school->hero_secondary_route ?: 'profile') === $route)>{{ $label }}</option>@endforeach</select></label>
            <label>Jenis tujuan kedua<select name="hero_secondary_target_type" data-target-type>@foreach(['route'=>'Halaman bawaan','page'=>'Halaman buatan admin','external'=>'URL eksternal'] as $value => $label)<option value="{{ $value }}" @selected(old('hero_secondary_target_type', $school->hero_secondary_target_type ?: 'route') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label data-target-field="page">Halaman buatan admin<select name="hero_secondary_page_id"><option value="">Pilih halaman terbit</option>@foreach(\App\Models\SitePage::where('is_published', true)->orderBy('title')->get() as $page)<option value="{{ $page->id }}" @selected((string) old('hero_secondary_page_id', $school->hero_secondary_page_id) === (string) $page->id)>{{ $page->title }}</option>@endforeach</select></label>
            <label class="wide" data-target-field="external">URL eksternal<input name="hero_secondary_external_url" type="url" value="{{ old('hero_secondary_external_url', $school->hero_secondary_external_url) }}" placeholder="https://contoh.sch.id" maxlength="500"></label>
        </div>
    </section>

    <section class="panel appearance-panel" aria-labelledby="section-settings-title">
        <div class="appearance-heading"><span class="eyebrow">03 / SUSUNAN</span><h2 id="section-settings-title">Bagian beranda</h2><p>Pilih bagian yang tampil dan atur urutannya. Isi berita, agenda, program, dan guru tetap mengikuti konten yang diterbitkan.</p></div>
        <div class="builder-section-list">
            @foreach(\App\Models\SchoolProfile::HOME_SECTIONS as $key => $defaults)
                @php($section = $school->homeSection($key))
                <div class="builder-section-row"><strong>{{ $defaults['label'] }}</strong><label class="check"><input type="hidden" name="home_sections[{{ $key }}][enabled]" value="0"><input type="checkbox" name="home_sections[{{ $key }}][enabled]" value="1" @checked(old('home_sections.'.$key.'.enabled', $section['enabled']))> Tampilkan</label><label>Urutan<input type="number" name="home_sections[{{ $key }}][order]" value="{{ old('home_sections.'.$key.'.order', $section['order']) }}" min="0" max="999" required></label></div>
            @endforeach
        </div>
    </section>

    <section class="panel appearance-panel" aria-labelledby="theme-settings-title">
        <div class="appearance-heading"><span class="eyebrow">04 / TEMA</span><h2 id="theme-settings-title">Warna dan bentuk</h2><p>Gunakan pilihan yang tersedia agar halaman tetap terbaca dan rapi di ponsel.</p></div>
        <div class="appearance-fields">
            <label>Warna utama<input type="color" name="theme_primary_color" value="{{ old('theme_primary_color', $school->theme_primary_color ?: '#2459d8') }}"></label>
            <label>Warna aksen<input type="color" name="theme_accent_color" value="{{ old('theme_accent_color', $school->theme_accent_color ?: '#1b7661') }}"></label>
            <label>Tipografi<select name="theme_font">@foreach(['jakarta'=>'Plus Jakarta Sans','system'=>'Huruf sistem','serif'=>'Serif'] as $value => $label)<option value="{{ $value }}" @selected(old('theme_font', $school->theme_font ?: 'jakarta') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>Header<select name="header_style">@foreach(['standard'=>'Standar','compact'=>'Ringkas'] as $value => $label)<option value="{{ $value }}" @selected(old('header_style', $school->header_style ?: 'standard') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>Bentuk kartu<select name="card_style">@foreach(['rounded'=>'Sudut lembut','square'=>'Sudut tegas'] as $value => $label)<option value="{{ $value }}" @selected(old('card_style', $school->card_style ?: 'rounded') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>Ukuran hero<select name="hero_style">@foreach(['full'=>'Lapang','compact'=>'Ringkas'] as $value => $label)<option value="{{ $value }}" @selected(old('hero_style', $school->hero_style ?: 'full') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="wide">Nama menu tambahan<input name="navigation_more_label" value="{{ old('navigation_more_label', $school->navigation_more_label ?: 'Informasi') }}" maxlength="24" required></label>
        </div>
        <p class="appearance-help"><a href="{{ route('manage.menu') }}">Atur menu dan tautan</a> untuk mengganti isi navigasi dan tombol header.</p>
    </section>

    <div class="appearance-actions"><button class="button" type="submit">Simpan tampilan website</button></div>
</form>
@endsection
