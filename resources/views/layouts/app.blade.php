<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OpenKas Skensa') }}</title>
    

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/wallet-icon.png') }}">

    <style>
        /* --- CSS KHUSUS NAVIGASI TENGAH --- */
        @media (min-width: 768px) {
            .absolute-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }
        }
        /* Agar sisi kiri/kanan tidak menutupi menu tengah */
        .navbar-brand, .ms-auto { z-index: 10; position: relative; }

        /* Style Cropper & Avatar */
        .img-container img { max-width: 100%; }
        .nav-avatar {
            width: 35px; height: 35px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--skensa-blue);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1">
        
        <nav class="navbar navbar-expand-md navbar-light bg-white sticky-top border-bottom">
            <div class="container position-relative"> <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="bi bi-wallet2 fs-3" style="color: var(--skensa-blue);"></i>
                    <span class="fw-bold" style="color: var(--skensa-dark-blue); letter-spacing: -0.5px;">OpenKas</span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav absolute-center fw-bold small text-uppercase ls-1">
                        @guest
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/') }}">Beranda</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#daftar-kelas') }}">Kelas</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#fitur') }}">Fitur</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{ url('/#faq') }}">FAQ</a></li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link px-3 {{ Request::is('/') ? 'text-primary' : '' }}" href="{{ url('/') }}"><i class="bi bi-house-door me-1"></i> Beranda</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 {{ Request::is('dashboard') ? 'text-primary' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 {{ Request::is('dashboard/students*') ? 'text-primary' : '' }}" href="{{ route('admin.students.index') }}"><i class="bi bi-people me-1"></i> Siswa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 {{ Request::is('dashboard/transactions*') ? 'text-primary' : '' }}" href="{{ route('admin.transactions.index') }}"><i class="bi bi-cash-stack me-1"></i> Transaksi</a>
                            </li>
                        @endguest
                    </ul>

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
                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3 p-2" style="min-width: 220px;">
                                    <div class="px-3 py-2 border-bottom mb-2">
                                        <span class="fw-bold d-block text-dark">{{ Auth::user()->name }}</span>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                    <button class="dropdown-item rounded-2 py-2" data-bs-toggle="modal" data-bs-target="#profileModal">
                                        <i class="bi bi-gear me-2 text-secondary"></i> Edit Profil
                                    </button>
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

        <main>
            @yield('content')
        </main>
    </div>

    @auth
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-circle me-2"></i>Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img id="avatarPreview" 
                                 src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4A7AB3&color=fff' }}" 
                                 class="rounded-circle border border-3 border-white shadow" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            
                            <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-white text-primary border border-primary rounded-circle p-2 shadow-sm" style="cursor: pointer; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Ganti Foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            
                            @if(Auth::user()->avatar)
                                <form action="{{ route('admin.profile.delete_avatar') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus foto profil?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="position-absolute top-0 end-0 bg-danger text-white border border-white rounded-circle p-1 shadow-sm" style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; transform: translate(20%, -20%);" title="Hapus Foto">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="file" id="avatarInput" class="d-none" accept="image/*">
                        <input type="hidden" name="avatar_cropped" id="avatarCropped">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Baru (Opsional)</label>
                            <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin ubah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password baru">
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Potong Foto (1:1)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="img-container">
                        <img id="imageToCrop" src="" style="max-width: 100%; display: block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="cropButton">Potong & Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var avatarInput = document.getElementById('avatarInput');
            var imageToCrop = document.getElementById('imageToCrop');
            var avatarPreview = document.getElementById('avatarPreview');
            var avatarCroppedInput = document.getElementById('avatarCropped');
            var cropper;
            var cropperModalEl = document.getElementById('cropperModal');

            if(cropperModalEl) {
                var cropperModal = new bootstrap.Modal(cropperModalEl);

                avatarInput.addEventListener('change', function (e) {
                    var files = e.target.files;
                    var done = function (url) {
                        imageToCrop.src = url;
                        cropperModal.show();
                    };
                    if (files && files.length > 0) {
                        var reader = new FileReader();
                        reader.onload = function (event) { done(reader.result); };
                        reader.readAsDataURL(files[0]);
                    }
                });

                cropperModalEl.addEventListener('shown.bs.modal', function () {
                    cropper = new Cropper(imageToCrop, { aspectRatio: 1, viewMode: 1, autoCropArea: 1 });
                });

                cropperModalEl.addEventListener('hidden.bs.modal', function () {
                    cropper.destroy();
                    cropper = null;
                    avatarInput.value = '';
                });

                document.getElementById('cropButton').addEventListener('click', function () {
                    var canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
                    canvas.toBlob(function (blob) {
                        var reader = new FileReader();
                        reader.readAsDataURL(blob); 
                        reader.onloadend = function () {
                            avatarCroppedInput.value = reader.result;
                            avatarPreview.src = reader.result;
                            cropperModal.hide();
                        }
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>