@extends('layouts.manage')
@section('title', 'Profil guru')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">SEKOLAH</span><h1>Profil guru</h1><p>Terbitkan hanya data dan foto yang disetujui pemiliknya.</p></div><a class="button" href="{{ route('manage.teachers.create') }}">Tambah guru</a></header><div class="panel"><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Jabatan</th><th>Status</th><th></th></tr></thead><tbody>@forelse($teachers as $teacher)<tr><td><strong>{{ $teacher->name }}</strong></td><td>{{ $teacher->position }}</td><td>{{ $teacher->is_published && $teacher->publication_approved ? 'Terbit' : 'Belum terbit' }}</td><td><a href="{{ route('manage.teachers.edit', $teacher) }}">Ubah ↗</a></td></tr>@empty<tr><td colspan="4">Belum ada profil guru.</td></tr>@endforelse</tbody></table></div>{{ $teachers->links() }}</div>
@endsection
