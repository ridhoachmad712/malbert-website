<article class="story-card">
    <a class="story-image" href="{{ $post->publicUrl() }}" aria-label="Baca {{ $post->title }}">
        @if($post->cover_path)<img src="{{ asset('storage/'.$post->cover_path) }}" alt="{{ $post->cover_alt ?: '' }}" loading="lazy">@else<span aria-hidden="true">{{ ['news'=>'✳','announcement'=>'↗','gallery'=>'▧'][$post->type] }}</span>@endif
    </a>
    <div class="story-body">
        <small>{{ ['news'=>'Berita','announcement'=>'Pengumuman','gallery'=>'Galeri'][$post->type] }} · {{ $post->published_at?->translatedFormat('d M Y') }}</small>
        <h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
        <p>{{ $post->summary ?: Str::limit(strip_tags($post->body), 130) }}</p>
        <a class="text-link" href="{{ $post->publicUrl() }}">Baca selengkapnya <span aria-hidden="true">↗</span></a>
    </div>
</article>
