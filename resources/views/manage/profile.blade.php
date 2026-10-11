@extends('layouts.manage')
@section('title', 'Profil sekolah')
@section('content')
<header class="manage-title"><span class="eyebrow">IDENTITAS</span><h1>Profil sekolah</h1><p>Gunakan data resmi yang telah diperiksa sekolah. Bagian kosong akan ditandai sebagai belum dilengkapi.</p></header><form class="panel form-grid" method="post" enctype="multipart/form-data" action="{{ route('manage.profile.update') }}">@csrf @method('PUT')
@foreach(['name'=>'Nama resmi sekolah','tagline'=>'Kalimat pengantar','npsn'=>'NPSN','address'=>'Alamat','email'=>'Email resmi','phone'=>'Telepon resmi','hours'=>'Jam layanan','map_url'=>'Tautan peta','aksikita_url'=>'Tautan AksiKita'] as $field => $label)<label @class(['wide' => $field === 'address'])>{{ $label }}<input name="{{ $field }}" value="{{ old($field, $school->$field) }}" @if($field === 'name')required @endif></label>@endforeach
@foreach(['description'=>'Deskripsi sekolah','history'=>'Sejarah singkat','vision'=>'Visi','mission'=>'Misi','programs'=>'Program sekolah','facilities'=>'Fasilitas'] as $field => $label)<label class="wide">{{ $label }}<textarea name="{{ $field }}" rows="4">{{ old($field, $school->$field) }}</textarea></label>@endforeach
<label>Logo sekolah<input type="file" name="logo" accept="image/*"><small>Maksimal 2 MB.</small></label><label>Foto utama beranda<input type="file" name="hero" accept="image/*"><small>Maksimal 4 MB; hanya foto yang boleh dipublikasikan.</small></label>
<div class="wide form-actions"><button class="button" type="submit">Simpan profil sekolah</button></div></form>
@endsection
