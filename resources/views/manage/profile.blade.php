@extends('layouts.manage')
@section('title', 'Profil sekolah')
@section('content')
<header class="manage-title"><span class="eyebrow">IDENTITAS</span><h1>Profil sekolah</h1><p>Gunakan data resmi yang telah diperiksa sekolah. Bagian kosong akan ditandai sebagai belum dilengkapi.</p></header>
<nav class="manage-section-nav" aria-label="Bagian profil sekolah">
    <a href="#identitas">Identitas</a>
    <a href="#cerita-sekolah">Tentang sekolah</a>
    <a href="#kepala-sekolah">Sambutan</a>
    <a href="#penerimaan">Penerimaan murid</a>
</nav>
<form class="manage-profile-form" method="post" enctype="multipart/form-data" action="{{ route('manage.profile.update') }}">
    @csrf @method('PUT')
    <section id="identitas" class="panel manage-profile-section" aria-labelledby="identitas-title">
        <h2 id="identitas-title">Identitas dan kontak</h2>
        <p>Informasi ini membantu pengunjung mengenali dan menghubungi sekolah.</p>
        <div class="form-grid">
            @foreach(['name'=>'Nama resmi sekolah','tagline'=>'Kalimat pengantar','npsn'=>'NPSN','address'=>'Alamat','email'=>'Email resmi','phone'=>'Telepon resmi','hours'=>'Jam layanan','map_url'=>'Tautan peta'] as $field => $label)
                <label @class(['wide' => $field === 'address'])>{{ $label }}<input name="{{ $field }}" value="{{ old($field, $school->$field) }}" @if($field === 'name')required @endif></label>
            @endforeach
        </div>
    </section>
    <section id="cerita-sekolah" class="panel manage-profile-section" aria-labelledby="cerita-title">
        <h2 id="cerita-title">Tentang sekolah</h2>
        <p>Isi profil, visi, misi, program, dan fasilitas dengan teks yang siap dibaca publik.</p>
        <div class="form-grid">
            @foreach(['description'=>'Deskripsi sekolah','history'=>'Sejarah singkat','vision'=>'Visi','mission'=>'Misi','programs'=>'Program sekolah','facilities'=>'Fasilitas'] as $field => $label)
                <label class="wide">{{ $label }}<textarea name="{{ $field }}" rows="4">{{ old($field, $school->$field) }}</textarea></label>
            @endforeach
        </div>
    </section>
    <section id="kepala-sekolah" class="panel manage-profile-section" aria-labelledby="kepala-title">
        <h2 id="kepala-title">Sambutan kepala sekolah</h2>
        <p>Isi nama dan sambutan resmi. Foto PNG kepala sekolah akan tampil di sisi kanan bagian sambutan pada beranda.</p>
        <div class="form-grid">
            <label class="wide">Nama kepala sekolah<input name="principal_name" value="{{ old('principal_name', $school->principal_name) }}" maxlength="180"></label>
            <div class="wide principal-photo-setting">
                @if($school->principal_photo_path)
                    <img src="{{ asset('storage/'.$school->principal_photo_path) }}" alt="Foto kepala sekolah saat ini">
                @endif
                <label>Foto kepala sekolah (PNG)<input type="file" name="principal_photo" accept="image/png"><small>Gunakan foto potret PNG dengan latar transparan, maksimal 4 MB.</small></label>
                @if($school->principal_photo_path)<label class="check"><input type="checkbox" name="remove_principal_photo" value="1"> Hapus foto saat ini</label>@endif
            </div>
            <label class="wide">Sambutan<textarea name="principal_greeting" rows="5">{{ old('principal_greeting', $school->principal_greeting) }}</textarea></label>
        </div>
    </section>
    <section id="penerimaan" class="panel manage-profile-section" aria-labelledby="penerimaan-title">
        <h2 id="penerimaan-title">Penerimaan murid</h2>
        <p>Isi jadwal dan ketentuan setelah sekolah mengumumkannya secara resmi.</p>
        <div class="form-grid">
            @foreach(['admission_intro'=>'Pengantar','admission_requirements'=>'Persyaratan','admission_steps'=>'Alur pendaftaran','admission_schedule'=>'Jadwal'] as $field => $label)
                <label class="wide">{{ $label }}<textarea name="{{ $field }}" rows="4">{{ old($field, $school->$field) }}</textarea></label>
            @endforeach
            <label class="wide">Tautan pendaftaran resmi<input type="url" name="admission_url" value="{{ old('admission_url', $school->admission_url) }}" maxlength="500"></label>
        </div>
    </section>
    <p class="manage-profile-note">Logo, gambar latar beranda, dan menu diatur pada <a href="{{ route('manage.appearance') }}">Susunan & tampilan</a>.</p>
    <div class="manage-profile-actions"><button class="button" type="submit">Simpan profil sekolah</button></div>
</form>
@endsection
