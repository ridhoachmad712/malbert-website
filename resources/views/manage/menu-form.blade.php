@extends('layouts.manage')
@section('title', $item->exists ? 'Ubah menu' : 'Tambah menu')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">NAVIGASI</span><h1>{{ $item->exists ? 'Ubah menu' : 'Tambah menu' }}</h1><p>Menu dapat menuju halaman website atau alamat HTTPS di luar website.</p></div><a class="button outline" href="{{ route('manage.menu') }}">Semua menu</a></header>
<form class="panel builder-form" method="post" action="{{ $item->exists ? route('manage.menu.update', $item) : route('manage.menu.store') }}">
    @csrf @if($item->exists)@method('PUT')@endif
    <div class="builder-grid">
        <label>Nama menu<input name="label" value="{{ old('label', $item->label) }}" maxlength="40" required></label>
        <label>Jenis tujuan<select name="target_type" data-target-type>@foreach(['route'=>'Halaman bawaan','page'=>'Halaman buatan admin','external'=>'URL eksternal'] as $value => $label)<option value="{{ $value }}" @selected(old('target_type', $item->target_type ?: 'route') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label data-target-field="route">Halaman bawaan<select name="target_route"><option value="">Pilih halaman</option>@foreach(\App\Models\SiteMenuItem::ROUTES as $key => $label)<option value="{{ $key }}" @selected(old('target_route', $item->target_route) === $key)>{{ $label }}</option>@endforeach</select></label>
        <label data-target-field="page">Halaman buatan admin<select name="site_page_id"><option value="">Pilih halaman terbit</option>@foreach($pages->where('is_published', true) as $page)<option value="{{ $page->id }}" @selected((string) old('site_page_id', $item->site_page_id) === (string) $page->id)>{{ $page->title }}</option>@endforeach</select></label>
        <label class="wide" data-target-field="external">URL eksternal<input type="url" name="external_url" value="{{ old('external_url', $item->external_url) }}" placeholder="https://contoh.sch.id" maxlength="500"><small>Gunakan alamat HTTPS lengkap.</small></label>
        <label>Letak<select name="location">@foreach(['primary'=>'Menu utama','more'=>'Menu Informasi','cta'=>'Tombol header'] as $value => $label)<option value="{{ $value }}" @selected(old('location', $item->location ?: 'more') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label>Urutan<input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 100) }}" min="0" max="999" required></label>
        @foreach(['is_enabled'=>'Tampilkan menu','show_in_footer'=>'Tampilkan juga di footer','open_new_tab'=>'Buka di tab baru'] as $field => $label)<label class="check wide"><input type="hidden" name="{{ $field }}" value="0"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $item->exists ? $item->$field : $field === 'is_enabled'))> {{ $label }}</label>@endforeach
    </div>
    <div class="builder-actions"><button class="button" type="submit">Simpan menu</button></div>
</form>
@if($item->exists)<form class="builder-delete" method="post" action="{{ route('manage.menu.destroy', $item) }}" onsubmit="return confirm('Hapus menu ini?')">@csrf @method('DELETE')<button type="submit">Hapus menu</button></form>@endif
@endsection
