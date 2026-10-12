@extends('layouts.manage')
@section('title', 'Pilih jenis konten')
@section('content')
<header class="manage-title"><a class="back-link" href="{{ route('manage.posts') }}">← Semua konten</a><span class="eyebrow">PUBLIKASI</span><h1>Pilih jenis konten</h1><p>Setiap jenis memiliki isian yang sesuai dengan kebutuhan publikasinya.</p></header>
<div class="content-type-grid">
@foreach($contentTypes as $type => $details)
<a class="content-type-card" href="{{ route('manage.posts.create-type', $type) }}"><strong>{{ $details['label'] }}</strong><span>{{ $details['description'] }}</span><small>Buat {{ $details['label'] }}</small></a>
@endforeach
</div>
@endsection
