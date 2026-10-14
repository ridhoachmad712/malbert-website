@extends('layouts.manage')
@section('title', 'Profil sekolah')
@section('content')
<header class="manage-title"><span class="eyebrow">IDENTITAS</span><h1>Profil sekolah</h1><p>Gunakan data resmi yang telah diperiksa sekolah. Bagian kosong akan ditandai sebagai belum dilengkapi.</p></header><form class="panel form-grid" method="post" enctype="multipart/form-data" action="{{ route('manage.profile.update') }}">@csrf @method('PUT')
@foreach(['name'=>'Nama resmi sekolah','tagline'=>'Kalimat pengantar','npsn'=>'NPSN','address'=>'Alamat','email'=>'Email resmi','phone'=>'Telepon resmi','hours'=>'Jam layanan','map_url'=>'Tautan peta'] as $field => $label)<label @class(['wide' => $field === 'address'])>{{ $label }}<input name="{{ $field }}" value="{{ old($field, $school->$field) }}" @if($field === 'name')required @endif></label>@endforeach
@foreach(['description'=>'Deskripsi sekolah','history'=>'Sejarah singkat','vision'=>'Visi','mission'=>'Misi','programs'=>'Program sekolah','facilities'=>'Fasilitas'] as $field => $label)<label class="wide">{{ $label }}<textarea name="{{ $field }}" rows="4">{{ old($field, $school->$field) }}</textarea></label>@endforeach
<div class="wide"><h2>Sambutan kepala sekolah</h2><p>Isi nama dan sambutan resmi sebelum ditampilkan pada website.</p></div>
<label class="wide">Nama kepala sekolah<input name="principal_name" value="{{ old('principal_name', $school->principal_name) }}" maxlength="180"></label>
<label class="wide">Sambutan<textarea name="principal_greeting" rows="5">{{ old('principal_greeting', $school->principal_greeting) }}</textarea></label>
<div class="wide"><h2>Penerimaan murid</h2><p>Isi jadwal dan ketentuan setelah sekolah mengumumkannya secara resmi.</p></div>
@foreach(['admission_intro'=>'Pengantar','admission_requirements'=>'Persyaratan','admission_steps'=>'Alur pendaftaran','admission_schedule'=>'Jadwal'] as $field => $label)<label class="wide">{{ $label }}<textarea name="{{ $field }}" rows="4">{{ old($field, $school->$field) }}</textarea></label>@endforeach
<label class="wide">Tautan pendaftaran resmi<input type="url" name="admission_url" value="{{ old('admission_url', $school->admission_url) }}" maxlength="500"></label>
<div class="wide"><p>Logo, gambar latar beranda, dan menu diatur pada <a class="text-link" href="{{ route('manage.appearance') }}">Tampilan website</a>.</p></div>
<div class="wide form-actions"><button class="button" type="submit">Simpan profil sekolah</button></div></form>
@endsection
