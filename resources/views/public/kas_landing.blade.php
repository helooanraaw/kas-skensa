@extends('layouts.app') @section('content')
<div class="container">
    <div class="jumbotron bg-light p-5 rounded-lg">
        <h1 class="display-4">Selamat Datang di OpenKas Skensa!</h1>
        <p class="lead">Ini adalah platform transparansi kas digital SMKN 1 Denpasar.</p>
        <hr class="my-4">
        <p>Dikelola oleh siswa, untuk siswa. Lihat progres keuangan kelasmu secara adil dan terbuka.</p>
        <a class="btn btn-primary btn-lg" href="{{ route('kas.index') }}" role="button">
            Lihat Kas Kelas Sekarang &raquo;
        </a>
    </div>
</div>
@endsection