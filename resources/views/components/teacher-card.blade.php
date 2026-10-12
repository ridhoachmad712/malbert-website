<article class="teacher-card">
    <a href="{{ route('teacher', $teacher->slug) }}" class="teacher-photo" aria-label="Profil {{ $teacher->name }}">
        @if($teacher->photo_path)<img src="{{ asset('storage/'.$teacher->photo_path) }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ mb_strtoupper(mb_substr($teacher->name, 0, 1)) }}</span>@endif
    </a>
    <div class="teacher-card-copy"><small>{{ $teacher->position }}</small><h3><a href="{{ route('teacher', $teacher->slug) }}">{{ $teacher->name }}</a></h3>@if($teacher->assignment)<p>{{ $teacher->assignment }}</p>@endif</div>
</article>
