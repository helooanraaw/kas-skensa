@extends('layouts.app')

{{-- SECTION CONTENT: Halaman Login Bendahara --}}
@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    
                    {{-- SIDEBAR KIRI (Logo & Info) - Hanya tampil di desktop --}}
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center p-5" style="background-color: var(--skensa-dark-blue);">
                        <div class="text-center text-white">
                            {{-- Logo Sekolah / Aplikasi --}}
                            <img src="{{ asset('images/skensa-logo.png') }}" 
                                alt="Logo SMKN 1 Denpasar" 
                                class="mb-4"
                                style="max-width: 180px !important; filter: drop-shadow(0 0 5px rgba(255,255,255,0.5));">
                            
                            <h3 class="fw-bold mb-3">OpenKas Skensa</h3>
                            <p class="text-white-50 small">
                                Sistem Manajemen Kas Digital XI RPL 1.<br>
                                Silakan login dengan akun Bendahara Anda.
                            </p>
                        </div>
                    </div>

                    {{-- SIDEBAR KANAN (Form Login) --}}
                    <div class="col-md-7 p-4 p-lg-5 bg-white">
                        <div class="text-center mb-5">
                            <h4 class="fw-bold mb-1" style="color: var(--skensa-dark-blue);">Selamat Datang</h4>
                            <p class="text-muted small">Masukkan kredensial akun Bendahara Anda.</p>
                        </div>

                        {{-- Form Input Login --}}
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- Alert Error jika Login Gagal --}}
                            @error('email')
                                <div class="alert alert-danger small border-0 shadow-sm mb-3">
                                    Email atau password salah.
                                </div>
                            @enderror
                            
                            {{-- Input Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold">Email Bendahara</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            </div>

                            {{-- Input Password --}}
                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">Password</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" style="background-color: var(--skensa-blue);">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Login') }}
                                </button>
                            </div>
                            
                            {{-- Link Kembali ke Home --}}
                            <div class="text-center">
                                <a class="text-decoration-none small text-muted" href="{{ url('/') }}">
                                    &laquo; Kembali ke Halaman Utama
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection