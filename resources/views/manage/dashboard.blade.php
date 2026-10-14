@extends('layouts.manage')
@section('title', 'Dasbor')
@section('content')
<header class="manage-title title-with-action">
    <div><span class="eyebrow">RINGKASAN</span><h1>Selamat datang, {{ auth()->user()->name }}</h1><p>Pilih pekerjaan yang ingin dilanjutkan di website sekolah.</p></div>
    <a class="button" href="{{ route('manage.posts.create') }}">Buat konten</a>
</header>
<div class="stat-grid">
    <div class="stat"><small>Semua konten</small><strong>{{ $postCount }}</strong><a href="{{ route('manage.posts') }}">Lihat daftar</a></div>
    <div class="stat"><small>Profil guru terbit</small><strong>{{ $teacherCount }}</strong>@if(auth()->user()->role === 'admin')<a href="{{ route('manage.teachers') }}">Kelola profil</a>@endif</div>
</div>
<section class="panel manage-task-panel" aria-labelledby="task-title">
    <div class="panel-heading"><div><h2 id="task-title">Yang bisa Anda kerjakan</h2><p>Akses cepat ke pekerjaan yang paling sering digunakan.</p></div></div>
    <div class="manage-task-grid">
        <a href="{{ route('manage.posts.create-type', 'news') }}"><strong>Tulis berita</strong><span>Bagikan kabar dan kegiatan sekolah.</span></a>
        <a href="{{ route('manage.posts.create-type', 'announcement') }}"><strong>Buat pengumuman</strong><span>Sampaikan informasi resmi kepada keluarga.</span></a>
        <a href="{{ route('manage.posts.create-type', 'event') }}"><strong>Tambah agenda</strong><span>Catat kegiatan dan jadwal mendatang.</span></a>
        @if(auth()->user()->role === 'admin')<a href="{{ route('manage.appearance') }}"><strong>Atur tampilan</strong><span>Sesuaikan beranda, logo, dan warna situs.</span></a>@endif
    </div>
</section>
<section class="panel" aria-labelledby="draft-title">
    <div class="panel-heading"><h2 id="draft-title">Draf dan tinjauan</h2><a class="manage-panel-link" href="{{ route('manage.posts') }}">Lihat semua konten</a></div>
    @forelse($drafts as $post)
        <div class="list-row"><div><strong>{{ $post->title }}</strong><small>{{ ['news'=>'Berita','announcement'=>'Pengumuman','event'=>'Agenda','gallery'=>'Galeri','program'=>'Program','facility'=>'Fasilitas','achievement'=>'Prestasi','faq'=>'Tanya jawab'][$post->type] ?? 'Konten' }} · {{ $post->status === 'review' ? 'Menunggu tinjauan' : 'Draf' }}</small></div><a class="button outline" href="{{ route('manage.posts.edit', $post) }}">Buka</a></div>
    @empty
        <p class="muted manage-empty-copy">Belum ada draf yang perlu dikerjakan.</p>
    @endforelse
</section>
@if(auth()->user()->role === 'admin' && $school->name === 'Nama sekolah belum diisi')<div class="notice"><strong>Lengkapi identitas sekolah.</strong> Nama, alamat, dan kontak resmi perlu diisi sebelum website digunakan. <a href="{{ route('manage.profile') }}">Buka profil sekolah</a></div>@endif
@endsection
