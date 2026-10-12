@extends('layouts.manage')
@section('title', ($post->exists ? 'Ubah ' : 'Tambah ').$contentType['label'])
@section('content')
<header class="manage-title">
    <a class="back-link" href="{{ route('manage.posts.type', $post->type) }}">← Daftar {{ $contentType['label'] }}</a>
    <span class="eyebrow">PUBLIKASI / {{ strtoupper($contentType['label']) }}</span>
    <h1>{{ $post->exists ? 'Ubah ' : 'Tambah ' }}{{ $contentType['label'] }}</h1>
    <p>{{ $post->exists ? 'Status: '.(['draft'=>'Draf','review'=>'Ditinjau','published'=>'Terbit','archived'=>'Arsip'][$post->status]) : 'Simpan sebagai draf, lalu tinjau sebelum diterbitkan.' }}</p>
</header>
@if($post->exists && $post->status === 'published' && auth()->user()->role === 'writer')
<div class="notice">Konten yang sudah terbit hanya dapat diubah oleh penerbit.</div>
@else
<form class="panel form-grid" method="post" enctype="multipart/form-data" action="{{ $post->exists ? route('manage.posts.update', $post) : route('manage.posts.store', $post->type) }}">
    @csrf @if($post->exists) @method('PUT') @endif
    <label class="wide">{{ $post->type === 'faq' ? 'Pertanyaan' : 'Judul' }}<input name="title" value="{{ old('title', $post->title) }}" maxlength="180" required></label>
    @if($post->type !== 'faq')
    <label class="wide">{{ $post->type === 'gallery' ? 'Keterangan singkat' : 'Ringkasan singkat' }}<textarea name="summary" rows="2" maxlength="450">{{ old('summary', $post->summary) }}</textarea></label>
    @endif
    @if($post->type === 'event')
    <label>Tanggal kegiatan<input type="date" name="event_date" value="{{ old('event_date', $post->event_date?->format('Y-m-d')) }}" required></label>
    <label>Lokasi kegiatan<input name="event_location" value="{{ old('event_location', $post->event_location) }}" maxlength="180"><small>Isi jika tempat sudah ditetapkan.</small></label>
    @endif
    @if($post->type === 'announcement')
    <label>Berlaku sampai<input type="date" name="expires_at" value="{{ old('expires_at', $post->expires_at?->format('Y-m-d')) }}"><small>Opsional. Setelah tanggal ini, pengumuman tidak tampil lagi.</small></label>
    @endif
    <label class="wide">{{ match($post->type) { 'faq' => 'Jawaban', 'event' => 'Rincian kegiatan', 'gallery' => 'Cerita di balik foto', 'announcement' => 'Isi pengumuman', default => 'Isi konten' } }}<textarea name="body" rows="12" required>{{ old('body', $post->body) }}</textarea><small>Gunakan baris kosong untuk memisahkan paragraf.</small></label>
    @if(in_array($post->type, ['news', 'gallery', 'program', 'facility', 'event', 'achievement'], true))
    <label>{{ $post->type === 'event' ? 'Poster atau foto kegiatan' : 'Foto utama' }}<input type="file" name="cover" accept="image/*"><small>Maksimal 4 MB. {{ $post->cover_path ? 'Foto saat ini tetap dipakai jika tidak diganti.' : '' }}</small></label>
    <label>Deskripsi foto<input name="cover_alt" value="{{ old('cover_alt', $post->cover_alt) }}" maxlength="250"><small>Jelaskan isi foto untuk pembaca layar.</small></label>
    @endif
    @if($post->type === 'gallery')
    <label class="wide">Foto galeri<input type="file" name="gallery_images[]" accept="image/*" multiple @if(! $post->gallery_paths) required @endif><small>{{ $post->gallery_paths ? count($post->gallery_paths).' foto tersimpan. Unggah foto tambahan bila perlu.' : 'Pilih minimal satu foto.' }} Maksimal 12 foto setiap unggahan, masing-masing 4 MB.</small></label>
    @endif
    @if(in_array($post->type, ['announcement', 'event'], true))
    <label class="wide">Lampiran PDF<input type="file" name="attachment" accept="application/pdf"><small>Opsional, maksimal 5 MB. Unggah hanya dokumen yang boleh diakses publik.</small></label>
    @endif
    @if($post->type === 'news')
    <label class="check wide"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured))> Tampilkan sebagai berita pilihan di beranda</label>
    @endif
    @if($post->type === 'achievement')
    <p class="wide form-note">Pastikan nama, capaian, dan izin publikasi sudah diperiksa sebelum diterbitkan.</p>
    @endif
    <div class="wide form-actions"><button class="button" type="submit">{{ $post->exists ? 'Simpan perubahan' : 'Simpan draf '.$contentType['label'] }}</button></div>
</form>
@endif
@if($post->exists)
<p><a class="text-link" href="{{ route('manage.posts.preview', $post) }}" target="_blank" rel="noopener">Lihat pratinjau privat</a></p>
<div class="panel"><h2>Alur penerbitan</h2><p>Hanya konten berstatus terbit yang dapat dilihat pengunjung.</p><div class="action-row">@if(auth()->user()->role === 'writer' && $post->status === 'draft')<form method="post" action="{{ route('manage.posts.status', $post) }}">@csrf<input type="hidden" name="action" value="review"><button class="button" type="submit">Ajukan tinjauan</button></form>@elseif(auth()->user()->role !== 'writer')@if($post->status !== 'published')<form method="post" action="{{ route('manage.posts.status', $post) }}">@csrf<input type="hidden" name="action" value="publish"><button class="button" type="submit">Terbitkan</button></form>@else<a class="button outline" href="{{ $post->publicUrl() }}" target="_blank" rel="noopener">Lihat halaman publik</a>@endif @if($post->status !== 'draft')<form method="post" action="{{ route('manage.posts.status', $post) }}">@csrf<input type="hidden" name="action" value="draft"><button class="button outline" type="submit">Kembalikan ke draf</button></form>@endif @if($post->status !== 'archived')<form method="post" action="{{ route('manage.posts.status', $post) }}">@csrf<input type="hidden" name="action" value="archive"><button class="plain-button" type="submit">Arsipkan</button></form>@endif @endif</div></div>
@endif
@endsection
