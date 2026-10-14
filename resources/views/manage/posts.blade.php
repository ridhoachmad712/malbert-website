@extends('layouts.manage')
@section('title', $type ? $contentTypes[$type]['label'] : 'Semua konten')
@section('content')
<header class="manage-title title-with-action">
    <div>
        <span class="eyebrow">PUBLIKASI</span>
        <h1>{{ $type ? $contentTypes[$type]['label'] : 'Semua konten' }}</h1>
        <p>{{ $type ? $contentTypes[$type]['description'] : 'Pilih jenis konten pada menu untuk melihat daftar dan membuat draf baru.' }}</p>
    </div>
    <a class="button" href="{{ $type ? route('manage.posts.create-type', $type) : route('manage.posts.create') }}">{{ $type ? 'Tambah '.$contentTypes[$type]['label'] : 'Pilih jenis konten' }}</a>
</header>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Judul</th><th>Jenis</th><th>Status</th><th>Diperbarui</th><th></th></tr></thead><tbody>
@forelse($posts as $post)
<tr><td><strong>{{ $post->title }}</strong></td><td>{{ $contentTypes[$post->type]['label'] }}</td><td><span class="status {{ $post->status }}">{{ ['draft'=>'Draf','review'=>'Ditinjau','published'=>'Terbit','archived'=>'Arsip'][$post->status] }}</span></td><td>{{ $post->updated_at->translatedFormat('d M Y') }}</td><td><a class="button outline" href="{{ route('manage.posts.edit', $post) }}">Ubah</a></td></tr>
@empty
<tr><td colspan="5">{{ $type ? 'Belum ada '.$contentTypes[$type]['label'].'.' : 'Belum ada konten.' }}</td></tr>
@endforelse
</tbody></table></div>{{ $posts->links() }}</div>
@endsection
