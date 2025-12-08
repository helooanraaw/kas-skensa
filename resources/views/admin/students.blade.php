@extends('layouts.app')

@section('content')
<div class="container pt-4 pb-5">
    
    {{-- ALERT MESSAGES: Menampilkan notifikasi sukses/gagal --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ROW INPUT DATA SISWA (Manual & Import) --}}
    <div class="row justify-content-center mb-4">
        {{-- Card Input Manual --}}
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Tambah 1 Siswa (Manual)</div>
                <div class="card-body">
                    <form action="{{ route('admin.students.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted">No. Absen</label>
                            <input type="number" class="form-control" name="nomor_absen" value="{{ old('nomor_absen') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Nama Siswa</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">NISN</label>
                            <input type="text" class="form-control" name="nisn" value="{{ old('nisn') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan Siswa</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card Import Excel --}}
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header text-white fw-bold" style="background-color: var(--skensa-dark-blue);">Import Siswa (Massal)</div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <p class="text-muted small mb-3">Upload file Excel untuk input data sekelas sekaligus.</p>
                    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background-color: var(--skensa-dark-blue); border: none;">Import Sekarang</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="{{ asset('templates/template_siswa.xlsx') }}" class="text-decoration-none small fw-bold">
                            <i class="bi bi-download me-1"></i> Download Template Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    {{-- ROW TABEL DATA SISWA --}}
    <div class="row justify-content-center">
        <div class="col-md-12"> 
            <div class="card border-0 shadow-sm">
                {{-- Toolbar Table: Judul & Sorting --}}
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
                    <span>Daftar Siswa & Peringkat</span>
                    
                    <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex gap-2">
                        <select name="sort" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()" style="min-width: 150px; cursor: pointer;">
                            <option value="absen" {{ $currentSort == 'absen' ? 'selected' : '' }}>Urut No. Absen</option>
                            <option value="tertinggi" {{ $currentSort == 'tertinggi' ? 'selected' : '' }}>Paling Rajin Bayar</option>
                            <option value="terendah" {{ $currentSort == 'terendah' ? 'selected' : '' }}>Paling Sedikit Bayar</option>
                        </select>
                    </form>
                </div>

                {{-- Tabel Siswa --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Rank</th>
                                    <th>Absen</th>
                                    <th>Nama Siswa</th>
                                    <th>Total Bayar</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr class="{{ $student->rank <= 3 ? 'bg-warning bg-opacity-10' : '' }}">
                                        {{-- Kolom Rank --}}
                                        <td class="ps-4">
                                            @if($student->rank == 1)
                                                <span class="badge bg-warning text-dark border border-warning rounded-pill">
                                                    <i class="bi bi-trophy-fill"></i> #1
                                                </span>
                                            @elseif($student->rank == 2)
                                                <span class="badge bg-secondary text-white border border-secondary rounded-pill">
                                                    <i class="bi bi-award-fill"></i> #2
                                                </span>
                                            @elseif($student->rank == 3)
                                                <span class="badge text-white border border-danger rounded-pill" style="background-color: #CD7F32;">
                                                    <i class="bi bi-award-fill"></i> #3
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border rounded-pill">
                                                    #{{ $student->rank }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="fw-bold text-muted">{{ $student->nomor_absen }}</td>
                                        
                                        <td>
                                            {{ $student->name }}
                                            <br>
                                            <small class="text-muted" style="font-size: 0.75rem;">NISN: {{ $student->nisn }}</small>
                                        </td>
                                        
                                        <td>
                                            <span class="fw-bold text-dark">
                                                Rp {{ number_format($student->total_paid ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        {{-- Kolom Aksi (Edit & Delete) --}}
                                        <td class="text-end pe-4">
                                            {{-- Tombol Edit (Modal) --}}
                                            <button type="button" class="btn btn-warning btn-sm px-3 text-white"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="{{ $student->id }}"
                                                data-absen="{{ $student->nomor_absen }}"
                                                data-name="{{ $student->name }}"
                                                data-nisn="{{ $student->nisn }}"
                                                data-url="{{ route('admin.students.update', $student->id) }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            {{-- Tombol Delete (Modal) --}}
                                            <button type="button" class="btn btn-danger btn-sm px-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-url="{{ route('admin.students.destroy', $student->id) }}"
                                                data-name="{{ $student->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                            Belum ada data siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

{{-- MODAL #1: EDIT SISWA --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">No. Absen</label>
                        <input type="number" class="form-control" id="edit_absen" name="nomor_absen" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Siswa</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">NISN</label>
                        <input type="text" class="form-control" id="edit_nisn" name="nisn" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white px-4 fw-bold">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL #2: HAPUS SISWA --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3"><i class="bi bi-exclamation-circle display-1"></i></div>
                <h5 class="fw-bold mb-2">Hapus Siswa?</h5>
                <p class="text-muted small mb-4">Data <span id="delete_name_display" class="fw-bold"></span> akan dihapus permanen.</p>
                <form id="deleteForm" method="POST" action="">
                    @csrf @method('DELETE')
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-bold">Ya, Hapus</button>
                        <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- SKRIP UNTUK MODAL EDIT & DELETE ---
    
    // 1. Skrip Modal Edit (Isi form dengan data yang diklik)
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var absen = button.getAttribute('data-absen');
        var name = button.getAttribute('data-name');
        var nisn = button.getAttribute('data-nisn');
        var url = button.getAttribute('data-url');
        document.getElementById('edit_absen').value = absen;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_nisn').value = nisn;
        document.getElementById('editForm').action = url;
    });

    // 2. Skrip Modal Delete (Set action form dengan URL delete)
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var url = button.getAttribute('data-url');
        var name = button.getAttribute('data-name');
        document.getElementById('deleteForm').action = url;
        document.getElementById('delete_name_display').textContent = name;
    });
</script>
@endpush