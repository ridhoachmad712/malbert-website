@extends('layouts.site')
@section('title', $title)
@section('content')
<div class="container page"><header class="page-intro"><span class="eyebrow">INFORMASI SEKOLAH</span><h1>{{ $title }}</h1><p>{{ ['news'=>'Cerita kegiatan dan kabar dari sekolah.','announcement'=>'Informasi resmi yang perlu diketahui warga sekolah.','gallery'=>'Dokumentasi kegiatan yang telah disetujui untuk dibagikan.','program'=>'Kegiatan belajar dan pengembangan yang diterbitkan sekolah.','facility'=>'Ruang dan sarana yang tersedia di sekolah.','event'=>'Jadwal kegiatan yang telah diumumkan sekolah.','achievement'=>'Capaian yang telah diperiksa dan diterbitkan sekolah.'][$type] }}</p></header>
@if($posts->isEmpty())<div class="empty"><strong>Belum ada {{ strtolower($title) }}.</strong><p>Konten akan tampil setelah diterbitkan pengelola sekolah.</p></div>@else<div class="card-grid">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>{{ $posts->links() }}@endif</div>
@endsection
