@extends('layouts.manage')
@section('title', 'Tampilan website')
@section('content')
<header class="manage-title title-with-action">
    <div><span class="eyebrow">SEKOLAH</span><h1>Tampilan website</h1><p>Atur identitas visual, beranda, dan menu yang terlihat oleh pengunjung.</p></div>
    <a class="button outline" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat beranda</a>
</header>
<form class="appearance-form" method="post" enctype="multipart/form-data" action="{{ route('manage.appearance.update') }}">
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
            <label>Tujuan tombol utama<select name="hero_primary_route">@foreach(\App\Models\SchoolProfile::HERO_LINKS as $route => $label)<option value="{{ $route }}" @selected(old('hero_primary_route', $school->hero_primary_route ?: 'admissions') === $route)>{{ $label }}</option>@endforeach</select></label>
            <label class="check wide"><input type="hidden" name="hero_secondary_enabled" value="0"><input type="checkbox" name="hero_secondary_enabled" value="1" @checked(old('hero_secondary_enabled', $school->hero_secondary_enabled ?? true))> Tampilkan tombol kedua</label>
            <label>Teks tombol kedua<input name="hero_secondary_label" value="{{ old('hero_secondary_label', $school->hero_secondary_label) }}" maxlength="40" placeholder="Profil sekolah"></label>
            <label>Tujuan tombol kedua<select name="hero_secondary_route">@foreach(\App\Models\SchoolProfile::HERO_LINKS as $route => $label)<option value="{{ $route }}" @selected(old('hero_secondary_route', $school->hero_secondary_route ?: 'profile') === $route)>{{ $label }}</option>@endforeach</select></label>
        </div>
    </section>

    <section class="panel appearance-panel" aria-labelledby="navigation-title">
        <div class="appearance-heading"><span class="eyebrow">03 / NAVIGASI</span><h2 id="navigation-title">Menu pengunjung</h2><p>Ubah nama, tempat, dan urutan menu. Menu yang dimatikan hilang dari navigasi dan footer; halamannya tetap dapat dibuka melalui tautan langsung.</p></div>
        <label class="appearance-more-label">Nama menu tambahan<input name="navigation_more_label" value="{{ old('navigation_more_label', $school->navigation_more_label ?: 'Informasi') }}" maxlength="24" required><small>Contoh: Informasi, Jelajahi, atau Lainnya.</small></label>
        <div class="appearance-menu-head" aria-hidden="true"><span>Tampilkan</span><span>Nama menu</span><span>Letak</span><span>Urutan</span></div>
        <div class="appearance-menu-list">
            @foreach($school->navigationItems() as $key => $item)
            <div class="appearance-menu-row">
                <label class="appearance-menu-toggle"><input type="hidden" name="navigation_items[{{ $key }}][enabled]" value="0"><input type="checkbox" name="navigation_items[{{ $key }}][enabled]" value="1" @checked(old('navigation_items.'.$key.'.enabled', $item['enabled']))><span class="sr-only">Tampilkan {{ $item['label'] }}</span></label>
                <label>Nama <span class="sr-only">{{ $item['label'] }}</span><input name="navigation_items[{{ $key }}][label]" value="{{ old('navigation_items.'.$key.'.label', $item['label']) }}" maxlength="32" required></label>
                <label>Letak <span class="sr-only">{{ $item['label'] }}</span><select name="navigation_items[{{ $key }}][location]">@foreach(['primary'=>'Menu utama','more'=>'Menu tambahan','cta'=>'Tombol utama'] as $value => $label)<option value="{{ $value }}" @selected(old('navigation_items.'.$key.'.location', $item['location']) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label>Urutan <span class="sr-only">{{ $item['label'] }}</span><input type="number" name="navigation_items[{{ $key }}][order]" value="{{ old('navigation_items.'.$key.'.order', $item['order']) }}" min="0" max="999" required></label>
            </div>
            @endforeach
        </div>
        <p class="appearance-help">Maksimal lima menu utama dan satu tombol utama agar navigasi tetap rapi. Tautan AksiKita tampil jika URL-nya diisi pada profil sekolah.</p>
    </section>

    <div class="appearance-actions"><button class="button" type="submit">Simpan tampilan website</button></div>
</form>
@endsection
