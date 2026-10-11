<article class="story-card">
    <a class="story-image" href="{{ $post->publicUrl() }}" aria-label="Baca {{ $post->title }}">
        @if($post->cover_path)<img src="{{ asset('storage/'.$post->cover_path) }}" alt="{{ $post->cover_alt ?: '' }}" loading="lazy">@else<span aria-hidden="true">{{ ['news'=>'✳','announcement'=>'↗','gallery'=>'▧','program'=>'✳','facility'=>'▣','event'=>'◇','achievement'=>'★'][$post->type] }}</span>@endif
    </a>
    <div class="story-body">
        <small>{{ ['news'=>'Berita','announcement'=>'Pengumuman','gallery'=>'Galeri','program'=>'Program','facility'=>'Fasilitas','event'=>'Agenda','achievement'=>'Prestasi'][$post->type] }} · {{ $post->type === 'event' ? $post->event_date?->translatedFormat('d M Y') : $post->published_at?->translatedFormat('d M Y') }}</small>
        <h3><a href="{{ $post->publicUrl() }}">{{ $post->title }}</a></h3>
        <p>{{ $post->summary ?: Str::limit(strip_tags($post->body), 130) }}</p>
        <a class="text-link" href="{{ $post->publicUrl() }}">Baca selengkapnya <span aria-hidden="true">↗</span></a>
    </div>
</article>
