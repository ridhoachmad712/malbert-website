@extends('layouts.manage')
@section('title', 'Menu & tautan')
@section('content')
<header class="manage-title title-with-action"><div><span class="eyebrow">WEBSITE</span><h1>Menu & tautan</h1><p>Atur navigasi, tombol header, dan tautan footer dari satu tempat.</p></div><a class="button" href="{{ route('manage.menu.create') }}">Tambah menu</a></header>
<section class="panel builder-list" aria-label="Daftar menu">
    @foreach($items as $item)
        <div class="builder-list-row"><div><strong>{{ $item->label }}</strong><small>{{ ['primary'=>'Menu utama','more'=>'Informasi','cta'=>'Tombol header'][$item->location] ?? $item->location }} · {{ $item->is_enabled ? 'Aktif' : 'Nonaktif' }} · {{ $item->url() ?: 'Tujuan belum tersedia' }}</small></div><div class="builder-list-actions"><a class="button outline" href="{{ route('manage.menu.edit', $item) }}">Atur</a></div></div>
    @endforeach
</section>
<p class="appearance-help">Maksimal lima menu utama dan satu tombol header. Halaman draf dan tujuan yang tidak tersedia tidak ditampilkan kepada pengunjung.</p>
@endsection
