@extends('layouts.manage')
@section('title', $teacher->exists ? 'Ubah profil guru' : 'Tambah guru')
@section('content')
<header class="manage-title"><a class="back-link" href="{{ route('manage.teachers') }}">← Daftar guru</a><span class="eyebrow">PENDIDIK</span><h1>{{ $teacher->exists ? 'Ubah profil guru' : 'Tambah profil guru' }}</h1><p>Hanya informasi yang diizinkan untuk publik yang dimasukkan di sini.</p></header><form class="panel form-grid" method="post" enctype="multipart/form-data" action="{{ $teacher->exists ? route('manage.teachers.update', $teacher) : route('manage.teachers.store') }}">@csrf @if($teacher->exists)@method('PUT')@endif
<label>Nama tampilan<input name="name" value="{{ old('name', $teacher->name) }}" required maxlength="180"></label><label>Jabatan atau peran<input name="position" value="{{ old('position', $teacher->position) }}" required maxlength="180"></label>
<label>Penugasan/kelas<input name="assignment" value="{{ old('assignment', $teacher->assignment) }}" maxlength="180"></label><label>Urutan tampil<input type="number" name="sort_order" value="{{ old('sort_order', $teacher->sort_order ?? 0) }}" min="0" max="9999"></label>
<label class="wide">Perkenalan singkat<textarea name="bio" rows="5" maxlength="1500">{{ old('bio', $teacher->bio) }}</textarea></label><label class="wide">Foto guru<input type="file" name="photo" accept="image/*"><small>Maksimal 2 MB; opsional. Pastikan izin foto telah diberikan.</small></label>
<label class="check wide"><input type="checkbox" name="publication_approved" value="1" @checked(old('publication_approved', $teacher->publication_approved))> Guru telah menyetujui data dan foto untuk ditampilkan</label><label class="check wide"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $teacher->is_published))> Terbitkan profil</label>
<div class="wide form-actions"><button class="button" type="submit">Simpan profil guru</button></div></form>
@endsection
