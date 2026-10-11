@extends('layouts.manage')
@section('title', 'Konten sekolah')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">PUBLIKASI</span><h1>Berita, pengumuman & galeri</h1><p>Tulis draf, ajukan tinjauan, lalu terbitkan informasi yang sudah diperiksa.</p></div><a class="button" href="{{ route('manage.posts.create') }}">Buat konten</a></header>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Judul</th><th>Jenis</th><th>Status</th><th>Diperbarui</th><th></th></tr></thead><tbody>@forelse($posts as $post)<tr><td><strong>{{ $post->title }}</strong></td><td>{{ ['news'=>'Berita','announcement'=>'Pengumuman','gallery'=>'Galeri'][$post->type] }}</td><td><span class="status {{ $post->status }}">{{ ['draft'=>'Draf','review'=>'Ditinjau','published'=>'Terbit','archived'=>'Arsip'][$post->status] }}</span></td><td>{{ $post->updated_at->translatedFormat('d M Y') }}</td><td><a href="{{ route('manage.posts.edit', $post) }}">Buka ↗</a></td></tr>@empty<tr><td colspan="5">Belum ada konten. Mulai dengan membuat draf.</td></tr>@endforelse</tbody></table></div>{{ $posts->links() }}</div>
@endsection
