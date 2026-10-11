@extends('layouts.site')
@section('title', 'Masuk pengelola')
@section('content')
<div class="container login-wrap"><div><span class="eyebrow">RUANG PENGELOLA</span><h1>Kelola kabar sekolah dengan tenang.</h1><p>Masuk untuk menulis berita, meninjau pengumuman, dan memperbarui profil sekolah.</p></div><form class="panel" method="post" action="{{ route('login.attempt') }}">@csrf<h2>Masuk</h2>@if($errors->any())<div class="notice error" role="alert">{{ $errors->first() }}</div>@endif<label>Email<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label><label>Kata sandi<input type="password" name="password" required autocomplete="current-password"></label><button class="button" type="submit">Masuk pengelola</button></form></div>
@endsection
