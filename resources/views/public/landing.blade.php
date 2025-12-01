@extends('layouts.app')

@section('content')

{{-- HERO SECTION --}}
<div class="position-relative w-100" style="background-color: var(--skensa-dark-blue); margin-top: -1px; padding-top: 6rem; padding-bottom: 14rem; color: white;">
    
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 animate from-left hero-stagger" data-stagger="90">
                <div class="d-inline-flex align-items-center border border-white border-opacity-10 rounded-pill px-3 py-1 mb-4 bg-white bg-opacity-5 backdrop-blur">
                    <span class="badge bg-success text-white rounded-pill me-2">AKTIF</span>
                    <small class="fw-bold text-black-50 letter-spacing-1">SEMESTER GANJIL 2024/2025</small>
                </div>

                <h1 class="display-3 fw-bold mb-4" style="line-height: 1.1; color: #fff; font-size: clamp(4rem,4.5vw,3rem);">
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

            <div class="col-lg-6 text-center animate from-right">
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
<div id="daftar-kelas" class="container animate from-bottom" style="margin-top: -8rem; position: relative; z-index: 10;">
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

{{-- TOP GLOBAL PAYERS (BARU) --}}
<div id="top-payers" class="container mt-4 animate from-bottom">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--skensa-dark-blue);">Top Pembayar Global</h5>
                <small class="text-muted">Siswa yang paling rajin membayar dari seluruh jurusan</small>
            </div>
        </div>
        <div class="card-body p-3">
            @if(isset($topPayers) && $topPayers->count())
                <div class="list-group">
                    @foreach($topPayers as $payer)
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-3 view-payer-btn" data-student-id="{{ $payer->student_id }}">
                            <div class="rank-badge me-2 rank-{{ $loop->iteration }}">#{{ $loop->iteration }}</div>
                            <div style="width:46px; height:46px; border-radius:50%; background:#eef3ff; display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--skensa-blue);">{{ strtoupper(substr($payer->student_name,0,1)) }}</div>
                            <div class="flex-grow-1 text-start ms-2">
                                <div class="fw-bold">{{ $payer->student_name }} <small class="text-muted">• {{ $payer->class_name }}</small></div>
                                <div class="small text-muted">Total bayar: Rp {{ number_format($payer->total_paid,0,',','.') }}</div>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="text-muted small">Belum ada data pembayaran.</div>
            @endif
        </div>
    </div>
</div>

{{-- LATAR BELAKANG & STATS SECTION --}}
<div id="literasi" class="py-5 mt-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0 animate from-left stagger-row">
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
            <div class="col-lg-6 offset-lg-1 animate from-right stagger-row">
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
                <div class="step-circle animate from-bottom">
                    <span class="step-number">1</span>
                </div>
                <h5 class="fw-bold text-dark">Siswa Bayar</h5>
                <p class="text-muted small px-4">Siswa membayar uang kas ke Bendahara (Tunai/Transfer).</p>
            </div>
            <div class="col-md-4 text-center position-relative">
                <div class="step-circle animate from-bottom">
                    <span class="step-number">2</span>
                </div>
                <h5 class="fw-bold text-dark">Bendahara Input</h5>
                <p class="text-muted small px-4">Bendahara login ke sistem dan mencatat transaksi (beserta foto bukti jika pengeluaran).</p>
                {{-- Arrow/garis putus-putus --}}
                <div class="d-none d-md-block position-absolute top-0 start-0 w-100 h-100" style="z-index: -1; top: 20px;">
                    <div style="border-top: 2px dashed #ccc; width: 50%; margin: 0 auto; transform: translateX(50%);"></div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-circle animate from-bottom">
                    <span class="step-number">3</span>
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
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
                    <div class="mb-3 text-primary"><i class="bi bi-file-earmark-spreadsheet fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Import Excel</h5>
                    <p class="text-muted small mb-0">Input data 36 siswa sekaligus dalam hitungan detik. Tidak perlu ketik satu per satu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
                    <div class="mb-3 text-success"><i class="bi bi-download fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Export Laporan</h5>
                    <p class="text-muted small mb-0">Download rekap keuangan format .xlsx otomatis untuk laporan ke Wali Kelas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
                    <div class="mb-3 text-warning"><i class="bi bi-pie-chart fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Grafik Visual</h5>
                    <p class="text-muted small mb-0">Dashboard dilengkapi grafik donat dan area chart untuk analisis keuangan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
                    <div class="mb-3 text-danger"><i class="bi bi-camera fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Bukti Foto</h5>
                    <p class="text-muted small mb-0">Upload foto nota belanja sebagai bukti validitas setiap pengeluaran kelas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
                    <div class="mb-3 text-info"><i class="bi bi-phone fs-2"></i></div>
                    <h5 class="fw-bold text-dark">Mobile Responsive</h5>
                    <p class="text-muted small mb-0">Tampilan menyesuaikan layar HP. Ringan dan cepat diakses di jaringan sekolah.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border h-100 shadow-sm hover-shadow transition animate from-bottom">
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
    /* --- Scroll animations (local to landing page) --- */
    :root { --lk-anim-duration: 0.65s; --lk-anim-ease: cubic-bezier(.2,.9,.2,1); }
    @media (prefers-reduced-motion: reduce) {
        .animate { transition: none !important; opacity: 1 !important; transform: none !important; }
    }

    .animate {
        opacity: 0;
        transform: translateY(12px) scale(.985);
        transition: opacity var(--lk-anim-duration) var(--lk-anim-ease), transform var(--lk-anim-duration) var(--lk-anim-ease);
        will-change: opacity, transform;
    }
    .animate.from-left { transform: translateX(-40px) scale(.98); }
    .animate.from-right { transform: translateX(40px) scale(.98); }
    .animate.from-bottom { transform: translateY(30px) scale(.98); }
    .animate.in-view { opacity: 1; transform: translateX(0) translateY(0) scale(1); }
    /* Stagger helper: items inside a .stagger-row get incremental delays */
    .stagger-row .animate { transition-duration: var(--lk-anim-duration); }
    /* Slightly delay hero children for a graceful entrance */
    .hero-stagger .animate { transition-duration: calc(var(--lk-anim-duration) + 0.05s); }
</style>

<style>
/* --- Mobile responsive tweaks (only visual, no structural changes) --- */
@media (max-width: 991px) {
    /* Reduce hero vertical spacing on tablet/phone */
    .position-relative.w-100[style*="padding-top:"] {
        padding-top: 3.5rem !important;
        padding-bottom: 6.5rem !important;
    }

    /* Hero title smaller and tighter */
    .display-3 { font-size: 2rem !important; line-height: 1.05 !important; }
    .lead { font-size: 0.95rem !important; }

    /* Hero image: remove harsh 3D transform and reduce min-height */
    .position-relative .rounded-4[style*="transform"] { transform: none !important; min-height: 180px !important; }

    /* Daftar kelas: reduce overlap and spacing */
    #daftar-kelas { margin-top: -4rem !important; }

    /* Make daftar-kelas buttons clearly tappable */
    #daftar-kelas .btn { padding-top: .75rem !important; padding-bottom: .75rem !important; font-size: .95rem; }

    /* Cards: slightly reduced padding to fit narrow screens */
    .card .card-body { padding: 1rem !important; }

    /* Stat blocks: balance sizes */
    .display-4 { font-size: 1.6rem !important; }

    /* Improve touch targets for icon circles */
    .rounded-circle[style*="width:"] { width: 56px !important; height: 56px !important; }
}

@media (max-width: 575px) {
    /* Small phones: further reduce paddings and stack content comfortably */
    .position-relative.w-100[style*="padding-top:"] {
        padding-top: 2.5rem !important;
        padding-bottom: 4.5rem !important;
    }
    .display-3 { font-size: 1.5rem !important; }
    .lead { font-size: 0.9rem !important; }

    /* Ensure hero image doesn't overflow horizontally */
    .position-relative .rounded-4 img { object-fit: cover; height: auto; max-height: 220px; }

    /* Make daftar-kelas grid more vertical: force single column on extra-small screens */
    #daftar-kelas .col-6 { width: 50%; float: left; }
    @supports (display: grid) {
        #daftar-kelas .row.g-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: .5rem; }
    }

    /* Make transaction-like cards and feature cards more compact */
    .bg-white.p-4.rounded-4 { padding: 0.9rem !important; }

    /* Increase spacing for clickable elements to avoid mis-taps */
    .btn, .list-group-item, .accordion-button { padding: 0.7rem 0.9rem !important; }

    /* Footer spacing */
    footer .container { padding-left: .75rem; padding-right: .75rem; }
}

/* Respect reduced motion at the top-level too */
@media (prefers-reduced-motion: reduce) {
    .animate { transition: none !important; opacity: 1 !important; transform: none !important; }
}
</style>

<style>
/* Comprehensive mobile/touch responsive refinements (non-structural) */
:root { --lk-hero-padding-desktop: 6.5rem; }
html, body { -webkit-text-size-adjust: 100%; }
.container { padding-left: 1rem; padding-right: 1rem; }
/* Base scalable typography */
body { font-size: 16px; }
.display-3 { font-size: clamp(1.5rem, 3.8vw, 2.4rem); line-height: 1.06; }
.display-4 { font-size: clamp(1.25rem, 3.2vw, 1.8rem); }
.lead { font-size: clamp(0.9rem, 2.2vw, 1.05rem); color: rgba(255,255,255,0.85); }

/* Images responsive */
img { max-width: 100%; height: auto; display: block; }
.hero-screenshot { width: 100%; height: auto; border-radius: .75rem; box-shadow: 0 8px 20px rgba(2,6,23,0.28); }

/* CTA full-width on mobile, comfortable touch target */
@media (max-width: 767px) {
    .btn-primary.cta { width: 100%; display: inline-flex; justify-content: center; align-items: center; padding: .9rem 1rem; font-size: 1rem; border-radius: 999px; }
    .position-relative.w-100[style*="padding-top:"] { padding-top: 2.6rem !important; padding-bottom: 4.2rem !important; }
    #daftar-kelas { margin-top: -2rem !important; }
    /* Stack daftar-kelas to single column for narrow screens if needed */
    #daftar-kelas .row.g-2 > [class*="col-"] { width: 100% !important; max-width: 100% !important; float: none !important; display: block !important; }
    /* Reduce card shadows and spacing to keep visual density reasonable */ 
    .card { box-shadow: none !important; border: 1px solid rgba(15,23,42,0.06); }
    .card .card-body { padding: .85rem !important; }
    /* Reduce hero paragraph width and spacing so text wraps better */ 
    .hero-copy { max-width: 44ch; }
    /* Increase tap areas */
    .btn, .list-group-item, .nav-link { min-height: 44px; padding-top: .6rem; padding-bottom: .6rem; }
    /* Ensure screenshots and preview widgets don't overflow */
    .preview-frame { overflow: hidden; border-radius: .9rem; }
}

/* Remove hover-only effects on touch devices to avoid accidental hover state */
@media (pointer: coarse) {
    .hover-primary:hover { background-color: inherit !important; color: inherit !important; border-color: inherit !important; transform: none !important; }
    .hover-scale:hover { transform: none !important; }
}

/* Small JS helper: mark the document as touch-capable and avoid hover states */
</style>
<style>
/* Step circle (1-2-3) responsive styles */
.step-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 50%;
    border: 1px solid rgba(15,23,42,0.06);
    box-shadow: 0 6px 16px rgba(2,6,23,0.06);
    width: clamp(64px, 16vw, 100px);
    height: clamp(64px, 16vw, 100px);
    margin-bottom: 1rem;
}
.step-circle .step-number {
    display: block;
    font-weight: 700;
    color: var(--skensa-blue);
    font-size: clamp(1.25rem, 4.5vw, 2rem);
    line-height: 1;
}

/* Make the circles sit nicely on very small screens */
@media (max-width: 575px) {
    .step-circle { width: 72px; height: 72px; }
    .step-circle .step-number { font-size: 1.3rem; }
    .col-md-4.text-center { padding-top: .5rem; padding-bottom: .5rem; }
    /* reduce px around description to avoid overflow */
    .col-md-4 p { padding-left: .5rem; padding-right: .5rem; }
}

/* Slightly larger spacing on desktop */
@media (min-width: 992px) {
    .step-circle { margin-bottom: 1.25rem; }
}

/* Rank badge for Top Payers list */
.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg,#fff,#f1f8ff);
    border: 1px solid rgba(74,122,179,0.12);
    color: var(--skensa-blue);
    font-weight: 700;
    width: 44px;
    height: 44px;
    border-radius: 8px;
    box-shadow: 0 6px 14px rgba(2,6,23,0.06);
    flex-shrink: 0;
}

@media (max-width: 575px) {
    .rank-badge { width: 40px; height: 40px; font-size: .95rem; }
}

/* Special colors for top 3 */
.rank-1 { background: linear-gradient(180deg,#ffd54a,#ffb300); color: #1b1b1b; border-color: rgba(255,183,77,0.5); box-shadow: 0 6px 18px rgba(255,184,77,0.12); }
.rank-2 { background: linear-gradient(180deg,#e6eef8,#c7dff8); color: #0b3b66; border-color: rgba(99,137,206,0.14); box-shadow: 0 6px 14px rgba(11,59,102,0.06); }
.rank-3 { background: linear-gradient(180deg,#f2e6da,#e0c4a3); color: #4b2e0f; border-color: rgba(160,120,80,0.12); box-shadow: 0 6px 14px rgba(75,46,15,0.06); }

/* Make top-1 slightly larger to emphasize */
.rank-1 { transform: scale(1.06); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add a class on first touch to allow CSS to target touch devices
    function onFirstTouch() {
        document.documentElement.classList.add('is-touch');
        window.removeEventListener('touchstart', onFirstTouch);
    }
    window.addEventListener('touchstart', onFirstTouch, {passive: true});

    // Prevent accidental hover states on touch by removing :hover styles via class
    // (CSS above targets pointer:coarse; this is defensive for some browsers)
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Helper to set transition delays for children inside a container with data-stagger
        document.querySelectorAll('[data-stagger]').forEach(function(container){
            const items = Array.from(container.querySelectorAll('.animate'));
            const base = parseInt(container.getAttribute('data-stagger') || 60, 10);
            items.forEach(function(el, idx){
                const delay = (idx * base);
                el.style.transitionDelay = (delay) + 'ms';
                // also store for observer use
                el.dataset._animDelay = delay;
            });
        });

        // Make hero children slightly staggered (if present)
        document.querySelectorAll('.hero-stagger').forEach(function(h){
            const children = h.querySelectorAll('.animate');
            children.forEach(function(el, i){ el.style.transitionDelay = (i * 90) + 'ms'; });
        });

        if (prefersReduced) {
            // Respect user preference: reveal all immediately
            document.querySelectorAll('.animate').forEach(function(el){ el.classList.add('in-view'); });
            return;
        }

        const observer = new IntersectionObserver(function(entries, obs){
            entries.forEach(function(entry){
                if (entry.isIntersecting) {
                    // if element has inline transitionDelay already set by data-stagger, respect it
                    entry.target.classList.add('in-view');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

        // Observe each animate element; if it's already visible on load, trigger after short delay to sequence nicely
        document.querySelectorAll('.animate').forEach(function(el){
            observer.observe(el);
        });
    });
</script>

<!-- Modal: Student Transactions Detail -->
<div class="modal fade" id="studentTransactionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="payerInfo" class="mb-3"></div>
                <div id="payerTransactions" class="list-group"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
 </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var modalEl = document.getElementById('studentTransactionsModal');
    var bsModal = new bootstrap.Modal(modalEl);
    var payerInfo = document.getElementById('payerInfo');
    var payerTransactions = document.getElementById('payerTransactions');

    document.querySelectorAll('.view-payer-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var studentId = this.dataset.studentId;
            if (!studentId) return;
            payerInfo.innerHTML = '<div class="text-center py-3">Memuat...</div>';
            payerTransactions.innerHTML = '';
            bsModal.show();

            fetch('/student/' + studentId + '/transactions')
                .then(function(res){ return res.json(); })
                .then(function(data){
                    payerInfo.innerHTML = '<div class="fw-bold">' + data.name + ' <small class="text-muted">(' + (data.nisn||'N/A') + ')</small></div>' +
                        '<div class="small text-muted">Kelas: ' + (data.class_name || '-') + ' • Total Transaksi: ' + data.transactions.length + '</div>';

                    if (data.transactions.length === 0) {
                        payerTransactions.innerHTML = '<div class="text-muted small p-3">Belum ada transaksi.</div>';
                        return;
                    }

                    var html = '';
                    data.transactions.forEach(function(t){
                        html += '<div class="list-group-item d-flex justify-content-between align-items-start">' +
                                '<div>' +
                                '<div class="fw-bold">' + (t.type == 'masuk' ? 'Pemasukan' : 'Pengeluaran') + ' • ' + t.date + '</div>' +
                                '<div class="small text-muted">' + (t.description || '') + '</div>' +
                                '</div>' +
                                '<div class="text-end">Rp ' + (t.amount ? Number(t.amount).toLocaleString('id-ID') : '0') + '</div>' +
                                '</div>';
                    });

                    payerTransactions.innerHTML = html;
                })
                .catch(function(){
                    payerInfo.innerHTML = '<div class="text-danger">Gagal memuat data.</div>';
                });
        });
    });
});
</script>

@endsection