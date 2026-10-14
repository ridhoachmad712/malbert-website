@extends('layouts.manage')
@section('title', $page->exists ? 'Ubah halaman' : 'Buat halaman')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">HALAMAN</span><h1>{{ $page->exists ? 'Ubah halaman' : 'Buat halaman' }}</h1><p>Halaman baru mulai sebagai draf. Terbitkan setelah teks dan tampilannya diperiksa.</p></div><a class="button outline" href="{{ route('manage.pages') }}">Semua halaman</a></header>
<form class="panel builder-form" method="post" enctype="multipart/form-data" action="{{ $page->exists ? route('manage.pages.update', $page) : route('manage.pages.store') }}">
    @csrf @if($page->exists)@method('PUT')@endif
    <div class="builder-grid">
        <label>Judul halaman<input name="title" value="{{ old('title', $page->title) }}" maxlength="180" required></label>
        <label>Alamat singkat<input name="slug" value="{{ old('slug', $page->slug) }}" maxlength="180" placeholder="contoh: sejarah-sekolah" required><small>Alamat akan menjadi /halaman/alamat-singkat.</small></label>
        <label class="wide">Pengantar<textarea name="intro" rows="3" maxlength="500">{{ old('intro', $page->intro) }}</textarea></label>
        <label class="wide">Isi halaman<textarea name="body" rows="14" maxlength="50000">{{ old('body', $page->body) }}</textarea><small>Pisahkan paragraf dengan baris kosong.</small></label>
        <label>Susunan halaman<select name="layout">@foreach(['standard'=>'Standar','wide'=>'Lebar','split'=>'Teks dan gambar berdampingan'] as $value => $label)<option value="{{ $value }}" @selected(old('layout', $page->layout ?: 'standard') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label>Gambar halaman<input type="file" name="image" accept="image/png,image/jpeg,image/webp"><small>PNG, JPG, atau WebP. Maksimal 6 MB.</small></label>
        @if($page->image_path)<div class="wide builder-image-preview"><img src="{{ asset('storage/'.$page->image_path) }}" alt="Gambar halaman saat ini"></div>@endif
        <label class="wide">Ringkasan untuk mesin pencari<input name="seo_description" value="{{ old('seo_description', $page->seo_description) }}" maxlength="160"></label>
        <label class="check wide"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))> Terbitkan halaman</label>
    </div>
    <div class="builder-actions"><button class="button" type="submit">Simpan halaman</button>@if($page->exists)<a class="button outline" href="{{ route('manage.pages.preview', $page) }}" target="_blank" rel="noopener">Pratinjau</a>@endif</div>
</form>
@if($page->exists)<form class="builder-delete" method="post" action="{{ route('manage.pages.destroy', $page) }}" onsubmit="return confirm('Hapus halaman ini? Menu yang menuju ke sini akan disembunyikan.')">@csrf @method('DELETE')<button type="submit">Hapus halaman</button></form>@endif
@endsection
