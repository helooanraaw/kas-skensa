<!doctype html>
{{-- LAYOUT UTAMA APLIKASI --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF Token untuk keamanan request Ajax --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OpenKas Skensa') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/wallet-icon.png') }}">

    {{-- Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Styles: Bootstrap, Icons, Cropper, Custom CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    {{-- Animation Library --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- SweetAlert2 untuk Pop-up cantik --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Override Font Global */
        body { font-family: 'Inter', sans-serif !important; }
        
        /* CSS Khusus Navbar Tengah */
        @media (min-width: 768px) {
            .absolute-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }
        }
        .navbar-brand, .ms-auto { z-index: 10; position: relative; }
        .nav-avatar { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; border: 2px solid var(--skensa-blue); }
    </style>

    <style>
        /* Mobile drawer for collapsed navbar (visual only, no structural changes) */
        @media (max-width: 991px) {
            /* Expose a CSS variable for navbar height so overlay can align */
            .navbar { --navbar-height: 56px; z-index: 1070; }
            .navbar-toggler { position: relative; z-index: 1075; }
            .navbar-collapse {
                position: fixed;
                top: var(--navbar-height, 56px); /* height of navbar */
                left: 0;
                right: 0;
                height: calc(100vh - 56px);
                background: linear-gradient(180deg, rgba(255,255,255,0.98), #fff);
                padding: 1rem;
                overflow-y: auto;
                transform: translateY(-6px);
                transition: transform .18s ease, opacity .18s ease;
                box-shadow: 0 18px 40px rgba(2,6,23,0.12);
                z-index: 1050;
            }
            /* When collapse is not shown, hide it cleanly via CSS (avoids JS toggling flicker) */
            .navbar-collapse:not(.show) { visibility: hidden; opacity: 0; pointer-events: none; transform: translateY(-6px); }
            .navbar-collapse.show { visibility: visible; opacity: 1; pointer-events: auto; transform: translateY(0); }

            /* Overlay behind the drawer */
            .nav-drawer-overlay { position: fixed; top: var(--navbar-height,56px); left: 0; right: 0; bottom: 0; background: rgba(2,6,23,0.32); z-index: 1040; display: none; }
            .nav-drawer-overlay.show { display: block; }

            /* Improve nav link touch targets */
            .navbar-nav .nav-link { padding: .9rem 1rem; font-size: 1rem; border-radius: .5rem; }
            .navbar-nav .nav-link:hover { background: rgba(0,0,0,0.03); }

            /* Center section becomes vertical list */
            .absolute-center { position: static; transform: none; display: block; text-align: left; margin-bottom: .5rem; }

            /* Right side auth area spacing */
            .navbar-nav.ms-auto { margin-top: .5rem; }
            .nav-avatar { width: 42px; height: 42px; }
        }

        /* Desktop: subtle hover focus for nav items */
        @media (min-width: 992px) {
            .navbar-nav .nav-link { transition: color .12s ease, background .12s ease; }
            .navbar-nav .nav-link:hover { color: var(--skensa-blue); background: transparent; }
        }

        /* Active-like pill for current page */
        .nav-link.active, .nav-link.text-primary { background: rgba(74,122,179,0.08); border-radius: .5rem; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1">
        
        {{-- NAVBAR UTAMA --}}
        <nav class="navbar navbar-expand-md navbar-light bg-white sticky-top border-bottom">
            <div class="container position-relative">
                
                {{-- Logo Kiri --}}
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="bi bi-wallet2 fs-3" style="color: var(--skensa-blue);"></i>
                    <span class="fw-bold" style="color: var(--skensa-dark-blue); letter-spacing: -0.5px;">OpenKas</span>
                </a>

                {{-- Tombol Toggler Mobile --}}
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- Menu Navbar --}}
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    
                    {{-- Bagian Tengah (Menu Navigasi) --}}
                    <ul class="navbar-nav absolute-center fw-bold small text-uppercase ls-1">
                        @guest
                            {{-- Menu Tamu --}}
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/') }}">Beranda</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#daftar-kelas') }}">Kelas</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#fitur') }}">Fitur</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#faq') }}">FAQ</a></li>
                        @else
                            {{-- Menu Admin Login --}}
                            <li class="nav-item"><a class="nav-link px-3 {{ Request::is('/') ? 'text-primary' : '' }}" href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i> Beranda</a></li>
                            <li class="nav-item"><a class="nav-link px-3 {{ Request::is('dashboard') ? 'text-primary' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link px-3 {{ Request::is('dashboard/students*') ? 'text-primary' : '' }}" href="{{ route('admin.students.index') }}"><i class="bi bi-people me-1"></i> Siswa</a></li>
                            <li class="nav-item"><a class="nav-link px-3 {{ Request::is('dashboard/transactions*') ? 'text-primary' : '' }}" href="{{ route('admin.transactions.index') }}"><i class="bi bi-cash-stack me-1"></i> Transaksi</a></li>
                        @endguest
                    </ul>

                    {{-- Bagian Kanan (Auth & Profil) --}}
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold text-white shadow-sm">Login Admin</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="nav-avatar shadow-sm">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4A7AB3&color=fff" class="nav-avatar shadow-sm">
                                    @endif
                                </a>
                                {{-- Dropdown Menu Profil --}}
                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3 p-2" style="min-width: 220px;">
                                    <div class="px-3 py-2 border-bottom mb-2">
                                        <span class="fw-bold d-block text-dark">{{ Auth::user()->name }}</span>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                    <button class="dropdown-item rounded-2 py-2" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <i class="bi bi-gear me-2 text-secondary"></i> Edit Profil
                                    </button>
                                    <a class="dropdown-item rounded-2 py-2" href="{{ route('admin.settings.index') }}">
                                        <i class="bi bi-sliders me-2 text-secondary"></i> Pengaturan Kas
                                    </a>
                                    <div class="dropdown-divider my-2"></div>
                                    <a class="dropdown-item rounded-2 py-2 text-danger fw-bold" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Overlay untuk Mobile Drawer --}}
        <div class="nav-drawer-overlay" id="navDrawerOverlay" aria-hidden="true"></div>

        <main>
            {{-- KONTEN HALAMAN UTAMA --}}
            @yield('content')
        </main>
    </div>

    {{-- MODAL EDIT PROFIL (Hanya untuk User Login) --}}
    @auth
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-circle me-2"></i>Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Upload Avatar UI (Bulat) --}}
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" 
                                     src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4A7AB3&color=fff' }}" 
                                     class="rounded-circle border border-3 border-white shadow" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-white text-primary border border-primary rounded-circle p-2 shadow-sm" style="cursor: pointer; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                @if(Auth::user()->avatar)
                                    <button type="button" onclick="confirmDeleteAvatar()" class="position-absolute top-0 end-0 bg-danger text-white border border-white rounded-circle p-1 shadow-sm" style="width: 25px; height: 25px; transform: translate(20%, -20%); display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <input type="file" id="avatarInput" class="d-none" accept="image/*">
                        <input type="hidden" name="avatar_cropped" id="avatarCropped">
                        
                        {{-- Form Text Inputs --}}
                        <div class="mb-3"><label class="small fw-bold text-muted">Nama</label><input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required></div>
                        <div class="mb-3"><label class="small fw-bold text-muted">Email</label><input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required></div>
                        <hr>
                        <div class="mb-3"><label class="small fw-bold text-muted">Password Baru</label><input type="password" class="form-control" name="password"></div>
                        <div class="mb-3"><label class="small fw-bold text-muted">Konfirmasi Password</label><input type="password" class="form-control" name="password_confirmation"></div>
                        <div class="text-end"><button type="submit" class="btn btn-primary px-4 fw-bold">Simpan</button></div>
                    </div>
                </form>
                {{-- Form Hidden Delete Avatar --}}
                <form id="deleteAvatarForm" action="{{ route('admin.profile.delete_avatar') }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    
    {{-- MODAL CROPPER (Untuk memotong foto profil) --}}
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Potong Foto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><div class="img-container"><img id="imageToCrop" src="" style="max-width: 100%;"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" id="cropButton">Simpan</button></div></div></div></div>
    @endauth

    {{-- SCRIPTS LIB --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // 1. Inisialisasi Animasi Scroll (AOS)
        AOS.init({ duration: 800, once: true });

        // 2. Format Rupiah Otomatis (Global untuk semua input class 'input-rupiah')
        document.addEventListener('DOMContentLoaded', function() {
            const rupiahInputs = document.querySelectorAll('.input-rupiah');
            rupiahInputs.forEach(input => {
                if(input.value) input.value = formatRupiah(input.value);
                input.addEventListener('keyup', function(e) { input.value = formatRupiah(this.value); });
            });
            function formatRupiah(angka) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            }
        });

        // 3. SweetAlert2 Notification (POP-UP CANTIK)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                showConfirmButton: false,
                timer: 2000,
                background: '#f8f9fa',
                color: '#1C2A45'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session("error") }}',
                confirmButtonColor: '#DC3545'
            });
        @endif

        // 4. Logika Cropper (Foto Profil Management)
        var avatarInput = document.getElementById('avatarInput');
        if(avatarInput) {
            var imageToCrop = document.getElementById('imageToCrop');
            var avatarPreview = document.getElementById('avatarPreview');
            var avatarCroppedInput = document.getElementById('avatarCropped');
            var cropper;
            var cropperModalEl = document.getElementById('cropperModal');
            var cropperModal = new bootstrap.Modal(cropperModalEl);

            // Saat file gambar dipilih
            avatarInput.addEventListener('change', function (e) {
                var files = e.target.files;
                if (files && files.length > 0) {
                    var reader = new FileReader();
                    reader.onload = function (event) { imageToCrop.src = reader.result; cropperModal.show(); };
                    reader.readAsDataURL(files[0]);
                }
            });
            // Saat modal cropper terbuka
            cropperModalEl.addEventListener('shown.bs.modal', function () {
                cropper = new Cropper(imageToCrop, { aspectRatio: 1, viewMode: 1 });
            });
            // Saat modal cropper tertutup
            cropperModalEl.addEventListener('hidden.bs.modal', function () {
                cropper.destroy(); cropper = null; avatarInput.value = '';
            });
            // Saat tombol simpan crop ditekan
            document.getElementById('cropButton').addEventListener('click', function () {
                cropper.getCroppedCanvas({ width: 300, height: 300 }).toBlob(function (blob) {
                    var reader = new FileReader();
                    reader.readAsDataURL(blob); 
                    reader.onloadend = function () {
                        avatarCroppedInput.value = reader.result; // Simpan base64 ke hidden field
                        avatarPreview.src = reader.result; // Update preview
                        cropperModal.hide();
                    }
                });
            });
        }

        // 5. Konfirmasi Hapus Avatar (SweetAlert2)
        function confirmDeleteAvatar() {
            Swal.fire({
                title: 'Hapus Foto Profil?',
                text: "Foto profil akan dikembalikan ke inisial nama.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                background: '#fff',
                color: '#1C2A45',
                customClass: {
                    popup: 'rounded-2 shadow-lg border-0 font-inter',
                    title: 'fw-bold fs-5',
                    htmlContainer: 'text-muted small',
                    confirmButton: 'btn btn-danger btn-sm px-4 fw-bold rounded-pill shadow-sm',
                    cancelButton: 'btn btn-light btn-sm px-4 fw-bold rounded-pill border me-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        timer: 1000,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading() }
                    });
                    document.getElementById('deleteAvatarForm').submit();
                }
            });
        }
    </script>
    <script>
        // Mobile navbar drawer overlay control
        document.addEventListener('DOMContentLoaded', function () {
            var collapseEl = document.getElementById('navbarSupportedContent');
            var overlay = document.getElementById('navDrawerOverlay');
            if (!collapseEl || !overlay) return;

            // Listen to bootstrap collapse events
            collapseEl.addEventListener('show.bs.collapse', function () {
                overlay.classList.add('show');
                collapseEl.classList.remove('collapsed-hidden');
                document.body.style.overflow = 'hidden';
            });
            collapseEl.addEventListener('hidden.bs.collapse', function () {
                overlay.classList.remove('show');
                // CSS handles hidden state automatically via :not(.show)
                document.body.style.overflow = '';
            });

            // Clicking overlay closes the drawer
            overlay.addEventListener('click', function () {
                var bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
                bsCollapse.hide();
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>