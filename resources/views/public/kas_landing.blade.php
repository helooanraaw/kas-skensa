@extends('layouts.app') 

{{-- Section Konten Utama --}}
@section('content')
<div class="container">
    {{-- Jumbotron: Banner utama sederhana --}}
    <div class="jumbotron bg-light p-5 rounded-lg">
        {{-- Judul Selamat Datang --}}
        <h1 class="display-4">Selamat Datang di OpenKas Skensa!</h1>
        <p class="lead">Ini adalah platform transparansi kas digital SMKN 1 Denpasar.</p>
        <hr class="my-4">
        {{-- Deskripsi singkat --}}
        <p>Dikelola oleh siswa, untuk siswa. Lihat progres keuangan kelasmu secara adil dan terbuka.</p>
        
        {{-- Tombol CTA untuk melihat daftar kelas --}}
        <a class="btn btn-primary btn-lg" href="{{ route('kas.index') }}" role="button">
            Lihat Kas Kelas Sekarang &raquo;
        </a>
    </div>
</div>
@endsection