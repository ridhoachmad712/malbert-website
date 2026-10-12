@extends('layouts.site')
@section('title', 'Guru dan tenaga kependidikan')
@section('content')
<div class="container page teachers-page"><header class="page-intro"><span class="eyebrow">PROFIL PENDIDIK</span><h1>Guru dan tenaga kependidikan</h1><p>Kenali pendidik yang profilnya telah disetujui untuk ditampilkan sekolah.</p></header>
<form class="search-bar" method="get"><label for="teacher-search">Cari nama guru</label><div><input id="teacher-search" type="search" name="q" value="{{ request('q') }}" placeholder="Masukkan nama"><button class="button" type="submit">Cari guru</button></div></form>
@if($teachers->isEmpty())<div class="empty"><strong>{{ request('q') ? 'Guru tidak ditemukan.' : 'Profil guru belum diterbitkan.' }}</strong><p>{{ request('q') ? 'Coba nama lain atau lihat semua profil.' : 'Profil akan tampil setelah mendapat persetujuan untuk dipublikasikan.' }}</p>@if(request('q'))<a class="text-link" href="{{ route('teachers') }}">Lihat semua guru</a>@endif</div>@else<div class="teacher-grid">@foreach($teachers as $teacher)<x-teacher-card :teacher="$teacher" />@endforeach</div>{{ $teachers->links() }}@endif</div>
@endsection
