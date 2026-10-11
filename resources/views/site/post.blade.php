@extends('layouts.site')
@section('title', $post->title)
@section('description', $post->summary ?: Str::limit(strip_tags($post->body), 150))
@section('content')
<article class="container page article"><a class="back-link" href="{{ !empty($preview) ? route('manage.posts.edit', $post) : route(['berita'=>'news','pengumuman'=>'announcements','galeri'=>'gallery','program'=>'programs','fasilitas'=>'facilities','agenda'=>'events','prestasi'=>'achievements'][$type] ?? 'faq') }}">← {{ !empty($preview) ? 'Kembali ke pengelola' : 'Kembali ke '.$type }}</a>@if(!empty($preview))<div class="notice">Pratinjau privat · halaman ini belum tentu terbit.</div>@endif<header><span class="eyebrow">{{ strtoupper($type) }} · {{ $post->type === 'event' ? $post->event_date?->translatedFormat('d F Y') : $post->published_at?->translatedFormat('d F Y') }}</span><h1>{{ $post->title }}</h1>@if($post->summary)<p class="lead">{{ $post->summary }}</p>@endif @if($post->type === 'event' && $post->event_location)<p>Lokasi: {{ $post->event_location }}</p>@endif</header>
@if($post->cover_path)<figure class="article-cover"><img src="{{ asset('storage/'.$post->cover_path) }}" alt="{{ $post->cover_alt ?: '' }}"><figcaption>{{ $post->cover_alt }}</figcaption></figure>@endif
<div class="body-copy">{{ $post->body }}</div>
@if($post->gallery_paths)<div class="article-gallery">@foreach($post->gallery_paths as $path)<img src="{{ asset('storage/'.$path) }}" alt="Dokumentasi {{ $post->title }}" loading="lazy">@endforeach</div>@endif
@if($post->attachment_path)<p><a class="button" href="{{ asset('storage/'.$post->attachment_path) }}" target="_blank" rel="noopener">Buka lampiran PDF <span aria-hidden="true">↗</span></a></p>@endif</article>
@endsection
