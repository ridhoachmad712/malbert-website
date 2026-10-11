@extends('layouts.site')
@section('title', $teacher->name)
@section('content')
<div class="container page"><a class="back-link" href="{{ route('teachers') }}">← Semua guru</a><article class="teacher-detail"><div class="detail-photo">@if($teacher->photo_path)<img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="Foto {{ $teacher->name }}">@else<span aria-hidden="true">{{ mb_strtoupper(mb_substr($teacher->name, 0, 1)) }}</span>@endif</div><div><span class="eyebrow">PROFIL PENDIDIK</span><h1>{{ $teacher->name }}</h1><p class="lead">{{ $teacher->position }}</p>@if($teacher->assignment)<p><strong>Penugasan:</strong> {{ $teacher->assignment }}</p>@endif @if($teacher->bio)<div class="body-copy">{{ $teacher->bio }}</div>@endif</div></article></div>
@endsection
