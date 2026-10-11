@extends('layouts.site')
@section('title', 'Penerimaan murid')
@section('content')
<div class="container page">
    <header class="page-intro"><span class="eyebrow">INFORMASI PENDAFTARAN</span><h1>Penerimaan murid</h1><p>{{ $school->admission_intro ?: 'Informasi penerimaan murid akan diumumkan setelah jadwal dan ketentuan resmi ditetapkan sekolah.' }}</p></header>
    <div class="admission-grid">
        <section class="info-panel"><span class="eyebrow">01 / PERSIAPAN</span><h2>Persyaratan</h2><p class="body-copy">{{ $school->admission_requirements ?: 'Persyaratan resmi belum diumumkan.' }}</p></section>
        <section class="info-panel"><span class="eyebrow">02 / TAHAPAN</span><h2>Alur pendaftaran</h2><p class="body-copy">{{ $school->admission_steps ?: 'Alur pendaftaran resmi belum diumumkan.' }}</p></section>
        <section class="info-panel"><span class="eyebrow">03 / WAKTU</span><h2>Jadwal</h2><p class="body-copy">{{ $school->admission_schedule ?: 'Jadwal penerimaan belum diumumkan.' }}</p></section>
    </div>
    @if($school->admission_url)<p class="admission-action"><a class="button" href="{{ $school->admission_url }}" target="_blank" rel="noopener noreferrer">Buka pendaftaran resmi ↗</a></p>@endif
    <section class="contact-band"><div><span class="eyebrow">BUTUH BANTUAN?</span><h2>Hubungi sekolah</h2><p>Pastikan informasi pendaftaran melalui kontak resmi sekolah.</p></div><a class="button button-light" href="{{ route('contact') }}">Lihat kontak ↗</a></section>
</div>
@endsection
