@extends('layouts.app') 

{{-- Section Konten Utama --}}
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                {{-- Judul Card --}}
                <div class="card-header">Pilih Kelas</div>

                <div class="card-body">
                    <h3>Pilih Jurusan & Kelas Kamu:</h3>

                    <div class="row">
                        {{-- Loop untuk menampilkan semua kelas yang tersedia --}}
                        @foreach($classes as $class)
                            <div class="col-md-3 mb-3">
                                {{-- Tombol link menuju detail kas kelas --}}
                                <a href="{{ route('kas.show', $class->slug) }}" class="btn btn-outline-primary w-100">
                                    {{ $class->name }}
                                </a>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection