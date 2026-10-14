@extends('layouts.site')
@section('title', $page->title)
@section('description', $page->seo_description ?: $page->intro ?: $school->tagline)
@section('content')
<div class="container page custom-page custom-page-{{ $page->layout }}">
    @if($preview ?? false)<p class="notice">Pratinjau hanya untuk pengelola. Perubahan formulir perlu disimpan terlebih dahulu.</p>@endif
    <header class="page-intro"><span class="eyebrow">{{ $school->name }}</span><h1>{{ $page->title }}</h1>@if($page->intro)<p>{{ $page->intro }}</p>@endif</header>
    <div class="custom-page-body">
        @if($page->image_path)<img class="custom-page-image" src="{{ asset('storage/'.$page->image_path) }}" alt="Ilustrasi {{ $page->title }}">@endif
        @if($page->body)<div class="body-copy">{{ $page->body }}</div>@endif
    </div>
</div>
@endsection
