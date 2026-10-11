@extends('layouts.site')
@section('title', 'Tanya jawab')
@section('content')
<div class="container page">
    <header class="page-intro"><span class="eyebrow">BANTUAN</span><h1>Tanya jawab</h1><p>Jawaban untuk pertanyaan yang sering diajukan kepada sekolah.</p></header>
    @forelse($questions as $question)
        <details class="faq-item"><summary>{{ $question->title }}</summary><p class="body-copy">{{ $question->body }}</p></details>
    @empty
        <div class="empty"><strong>Pertanyaan belum ditambahkan.</strong><p>Silakan hubungi sekolah untuk memperoleh informasi yang Anda perlukan.</p></div>
    @endforelse
    <p class="faq-contact"><a class="text-link" href="{{ route('contact') }}">Lihat kontak resmi sekolah ↗</a></p>
</div>
@endsection
