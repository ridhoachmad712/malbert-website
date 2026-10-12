@extends('layouts.site')
@section('title', 'Kontak dan lokasi')
@section('content')
<div class="container page"><header class="page-intro"><span class="eyebrow">KONTAK SEKOLAH</span><h1>Kontak dan lokasi</h1><p>Alamat dan saluran yang tersedia untuk menghubungi sekolah.</p></header><div class="contact-grid"><section><h2>Alamat sekolah</h2><p>{{ $school->address ?: 'Alamat resmi belum dilengkapi.' }}</p>@if($school->map_url)<a class="text-link" href="{{ $school->map_url }}" target="_blank" rel="noopener">Buka peta lokasi</a>@endif</section><section><h2>Hubungi sekolah</h2><dl><dt>Email</dt><dd>@if($school->email)<a href="mailto:{{ $school->email }}">{{ $school->email }}</a>@else Belum dilengkapi @endif</dd><dt>Telepon</dt><dd>{{ $school->phone ?: 'Belum dilengkapi' }}</dd><dt>Jam layanan</dt><dd>{{ $school->hours ?: 'Belum dilengkapi' }}</dd></dl><a class="text-link" href="{{ route('faq') }}">Baca tanya jawab</a></section></div></div>
@endsection
