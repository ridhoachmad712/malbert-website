@extends('layouts.manage')
@section('title', 'Dasbor')
@section('content')
<header class="manage-title"><span class="eyebrow">RINGKASAN</span><h1>Selamat datang, {{ auth()->user()->name }}</h1><p>Mulai dari informasi yang perlu disiapkan untuk pengunjung sekolah.</p></header>
<div class="stat-grid"><div class="stat"><small>Semua konten</small><strong>{{ $postCount }}</strong></div><div class="stat"><small>Profil guru terbit</small><strong>{{ $teacherCount }}</strong></div></div>
<div class="panel"><div class="panel-heading"><h2>Draf dan tinjauan</h2><a class="text-link" href="{{ route('manage.posts') }}">Lihat semua ↗</a></div>@forelse($drafts as $post)<div class="list-row"><div><strong>{{ $post->title }}</strong><small>{{ ['news'=>'Berita','announcement'=>'Pengumuman','gallery'=>'Galeri'][$post->type] }} · {{ $post->status === 'review' ? 'Menunggu tinjauan' : 'Draf' }}</small></div><a href="{{ route('manage.posts.edit', $post) }}">Buka ↗</a></div>@empty<p class="muted">Belum ada draf yang perlu dikerjakan.</p>@endforelse</div>
@if(auth()->user()->role === 'admin' && $school->name === 'Nama sekolah belum diisi')<div class="notice"><strong>Lengkapi identitas sekolah.</strong> Nama, alamat, dan kontak resmi perlu diisi sebelum website digunakan. <a href="{{ route('manage.profile') }}">Buka profil sekolah ↗</a></div>@endif
@endsection
