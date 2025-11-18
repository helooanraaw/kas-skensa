@extends('layouts.app')

@section('content')

{{-- HERO SECTION --}}
<div class="position-relative w-100" style="background-color: var(--skensa-dark-blue); margin-top: -1px; padding-top: 6rem; padding-bottom: 14rem; color: white;">
    
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="d-inline-flex align-items-center border border-white border-opacity-10 rounded-pill px-3 py-1 mb-4 bg-white bg-opacity-5 backdrop-blur">
                    <span class="badge bg-success text-white rounded-pill me-2">AKTIF</span>
                    <small class="fw-bold text-black-50 letter-spacing-1">SEMESTER GANJIL 2024/2025</small>
                </div>

                <h1 class="display-3 fw-bold mb-4" style="line-height: 1.1; color: #fff;">
                    OpenKas Skensa,<br>
                    <span style="color: var(--skensa-blue);">Sistem Kas Digital.</span>
                </h1>

                <p class="lead text-white-50 mb-5 pe-lg-5" style="font-weight: 300;">
                    Platform pencatatan keuangan kelas berbasis web untuk lingkungan SMKN 1 Denpasar. 
                    Catat, pantau, dan laporkan dana kelas dengan sistem yang <strong>transparan</strong>, akurat, dan mudah diakses.
                </p>

                <div class="d-flex gap-3">
                    <a href="#daftar-kelas" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-lg border-0 rounded-pill d-flex align-items-center gap-3" style="background-color: var(--skensa-blue); transition: all 0.3s;">
                        <span>Cek Data Kas Sekarang</span>
                        <i class="bi bi-arrow-right-circle-fill fs-4"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <div class="rounded-4 shadow-lg overflow-hidden border border-4 border-white bg-dark" style="min-height: 300px; transform: perspective(1000px) rotateY(-5deg);">
                        
                        <img src="{{ asset('images/dashboard.png') }}" 
                            alt="Dashboard Preview" 
                            class="img-fluid w-100">
                            
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gradasi lebih pendek dan rapat ke bawah --}}
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 40px; background: linear-gradient(to bottom, transparent, #F8FAFC);"></div>
</div>

{{-- DAFTAR KELAS SECTION --}}
<div id="daftar-kelas" class="container" style="margin-top: -8rem; position: relative; z-index: 10;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-4 text-center">
            <h4 class="fw-bold mb-1" style="color: var(--skensa-dark-blue);">PILIH KELAS ANDA</h4>
            <p class="text-muted mb-0 small">Klik nama kelas untuk melihat laporan keuangan real-time.</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-2">
                @foreach($classes as $class)
                    <div class="col-6 col-md-3 col-lg-2">
                        <a href="{{ route('kas.show', $class->slug) }}" 
                           class="btn btn-light w-100 py-2 fw-bold text-truncate border hover-primary"
                           style="background-color: #F8FAFC;">
                            {{ $class->name }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- LATAR BELAKANG & STATS SECTION --}}
<div id="literasi" class="py-5 mt-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h6 class="text-uppercase fw-bold text-success ls-2 mb-3">Latar Belakang</h6>
                <h2 class="fw-bold mb-4 display-6" style="color: var(--skensa-dark-blue);">Mengapa Sistem Ini<br>Sangat Penting?</h2>
                <p class="text-secondary">
                    Masalah keuangan di kelas seringkali menjadi sumber konflik karena pencatatan yang tidak rapi. Buku hilang, selisih hitungan, hingga ketidakjelasan penggunaan dana adalah masalah klasik.
                </p>
                <p class="text-secondary">
                    <strong>OpenKas Skensa</strong> hadir bukan hanya sebagai alat hitung, tapi sebagai sarana edukasi. Kami mengajarkan integritas dan tanggung jawab melalui sistem yang transparan.
                </p>
                <div class="vstack gap-3 mt-4">
                    <div class="d-flex gap-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded text-success"><i class="bi bi-check-lg"></i></div>
                        <div><h6 class="fw-bold mb-0 text-dark">Cegah Manipulasi</h6><small class="text-secondary">Data digital sulit dipalsukan.</small></div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded text-primary"><i class="bi bi-check-lg"></i></div>
                        <div><h6 class="fw-bold mb-0 text-dark">Bukti Valid</h6><small class="text-secondary">Semua pengeluaran ada foto notanya.</small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-1">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-4 border h-100 text-center">
                            <h2 class="fw-bold text-primary display-4">24</h2>
                            <small class="fw-bold text-uppercase text-dark">Kelas Terdaftar</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-4 border h-100 text-center">
                            <h2 class="fw-bold text-success display-4">100%</h2>
                            <small class="fw-bold text-uppercase text-dark">Paperless</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-4 rounded-4 text-white h-100 d-flex align-items-center justify-content-center gap-3" style="background-color: var(--skensa-dark-blue);">
                            <i class="bi bi-shield-lock display-4"></i>
                            <div class="text-start">
                                <h5 class="fw-bold mb-0 text-white">Keamanan Data</h5>
                                <small class="text-white-50">Server Sekolah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CARA KERJA SISTEM SECTION --}}
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: var(--skensa-dark-blue);">Cara Kerja Sistem</h2>
            <p class="text-muted">Alur sederhana untuk Bendahara dan Siswa.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="bg-white p-4 rounded-circle shadow-sm d-inline-block mb-3 border" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <span class="h1 fw-bold text-primary mb-0">1</span>
                </div>
                <h5 class="fw-bold text-dark">Siswa Bayar</h5>
                <p class="text-muted small px-4">Siswa membayar uang kas ke Bendahara (Tunai/Transfer).</p>
            </div>
            <div class="col-md-4 text-center position-relative">
                <div class="bg-white p-4 rounded-circle shadow-sm d-inline-block mb-3 border" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <span class="h1 fw-bold text-primary mb-0">2</span>
                </div>
                <h5 class="fw-bold text-dark">Bendahara Input</h5>
                <p class="text-muted small px-4">Bendahara login ke sistem dan mencatat transaksi (beserta foto bukti jika pengeluaran).</p>
                {{-- Arrow/garis putus-putus --}}
                <div class="d-none d-md-block position-absolute top-0 start-0 w-100 h-100" style="z-index: -1; top: 20px;">
                    <div style="border-top: 2px dashed #ccc; width: 50%; margin: 0 auto; transform: translateX(50%);"></div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-white p-4 rounded-circle shadow-sm d-inline-block mb-3 border" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                    <span class="h1 fw-bold text-primary mb-0">3</span>
                </div>
                <h5 class="fw-bold text-dark">Data Update</h5>
                <p class="text-muted small px-4">Data otomatis terupdate. Siswa bisa langsung cek statusnya di website.</p>
            </div>
        </div>
    </div>
</div>

{{-- FITUR & KEUNGGULAN SECTION --}}
<div id="fitur" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: var(--skensa-dark-blue);">Fitur & Keunggulan</h2>
            <p class="text-muted">Teknologi yang kami gunakan untuk memudahkan Bendahara.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-primary"><i class="bi bi-file-earmark-spreadsheet fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Import Excel</h5>
                    <p class="text-muted small mb-0">Input data 36 siswa sekaligus dalam hitungan detik. Tidak perlu ketik satu per satu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-success"><i class="bi bi-download fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Export Laporan</h5>
                    <p class="text-muted small mb-0">Download rekap keuangan format .xlsx otomatis untuk laporan ke Wali Kelas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-warning"><i class="bi bi-pie-chart fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Grafik Visual</h5>
                    <p class="text-muted small mb-0">Dashboard dilengkapi grafik donat dan area chart untuk analisis keuangan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-danger"><i class="bi bi-camera fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Bukti Foto</h5>
                    <p class="text-muted small mb-0">Upload foto nota belanja sebagai bukti validitas setiap pengeluaran kelas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-info"><i class="bi bi-phone fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Mobile Responsive</h5>
                    <p class="text-muted small mb-0">Tampilan menyesuaikan layar HP. Ringan dan cepat diakses di jaringan sekolah.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition">
                    <div class="mb-3 text-dark"><i class="bi bi-lock fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Role Security</h5>
                    <p class="text-muted small mb-0">Hanya Bendahara yang bisa edit. Siswa lain hanya bisa melihat (Read-only).</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- VISI JANGKA PANJANG SECTION --}}
<div class="py-5 bg-light border-top border-bottom">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <i class="bi bi-compass fs-1 text-primary opacity-50 mb-3"></i>
                <h3 class="fw-bold mb-4" style="color: var(--skensa-dark-blue);">Visi Jangka Panjang</h3>
                <p class="lead text-secondary fst-italic mb-5">
                    "OpenKas Skensa dirancang bukan hanya sebagai tugas sesaat, melainkan sebagai pondasi digitalisasi administrasi kelas. Visi kami adalah menciptakan standar baru di mana setiap kelas di SMKN 1 Denpasar memiliki manajemen keuangan yang akuntabel, dapat diaudit, dan bebas dari sengketa."
                </p>
            </div>
        </div>
    </div>
</div>

{{-- FAQ SECTION --}}
<div id="faq" class="py-5" style="background-color: #F8FAFC;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-4">
                <h2 class="fw-bold text-dark">Pertanyaan Umum</h2>
            </div>
            <div class="col-lg-8">
                <div class="accordion shadow-sm" id="faqAccordion">
                    <div class="accordion-item border-0 mb-2">
                        <h2 class="accordion-header"><button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q1">Apakah data ini aman?</button></h2>
                        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body text-muted small">Ya. Sistem menggunakan database MySQL yang aman. Hanya Bendahara terdaftar yang memiliki password untuk mengubah data.</div></div>
                    </div>
                    <div class="accordion-item border-0 mb-2">
                        <h2 class="accordion-header"><button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">Bagaimana cara saya login?</button></h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body text-muted small">Login hanya untuk Bendahara. Akun dibuatkan oleh Admin Pusat. Siswa biasa tidak perlu login, cukup cari kelasnya di halaman depan.</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer class="pt-5 pb-4 text-white mt-auto" style="background-color: #111827;">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="bi bi-wallet2 fs-3" style="color: var(--bg-body);"></i>
                    <span class="fw-bold" style="color: var(--bg-body); letter-spacing: -0.5px;">OpenKas</span>
                </a>
                <p class="text-white-50 small">
                    Sistem Informasi Manajemen Kas Kelas.<br>
                    Mewujudkan transparansi di lingkungan sekolah.
                </p>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white mb-3">Menu</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                    <li class="mb-2"><a href="#daftar-kelas" class="text-white-50 text-decoration-none">Cek Kas</a></li>
                    <li class="mb-2"><a href="#fitur" class="text-white-50 text-decoration-none">Fitur</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white mb-3">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#faq" class="text-white-50 text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Login Staff</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold text-white mb-3">Pengembang</h6>
                <ul class="list-unstyled small text-white-50">
                    <li class="mb-2"><i class="bi bi-person-fill me-2"></i>I Nyoman Anrasansya D.P.</li>
                    <li class="mb-2"><i class="bi bi-mortarboard-fill me-2"></i>XI RPL 1 / Absen 13</li>
                    <li class="mb-2"><i class="bi bi-geo-alt-fill me-2"></i>SMK Negeri 1 Denpasar</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary opacity-25 my-4">
        <div class="text-center small text-white-50">
            &copy; {{ date('Y') }} OpenKas Skensa. All rights reserved.
        </div>
    </div>
</footer>

<style>
    .hover-primary:hover {
        background-color: var(--skensa-blue) !important;
        color: white !important;
        border-color: var(--skensa-blue) !important;
    }
    .backdrop-blur {
        backdrop-filter: blur(5px);
    }
</style>

@endsection