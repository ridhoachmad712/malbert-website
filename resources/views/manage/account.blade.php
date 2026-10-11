@extends('layouts.manage')
@section('title', 'Kata sandi')
@section('content')
<header class="manage-title"><span class="eyebrow">AKUN</span><h1>Ganti kata sandi</h1><p>Gunakan kata sandi panjang dan unik untuk akun pengelola website.</p></header><form class="panel account-form" method="post" action="{{ route('manage.account.update') }}">@csrf @method('PUT')<label>Kata sandi saat ini<input type="password" name="current_password" required autocomplete="current-password"></label><label>Kata sandi baru<input type="password" name="password" minlength="12" required autocomplete="new-password"></label><label>Ulangi kata sandi baru<input type="password" name="password_confirmation" minlength="12" required autocomplete="new-password"></label><button class="button" type="submit">Simpan kata sandi baru</button></form>
@endsection
