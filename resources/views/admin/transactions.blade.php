@extends('layouts.app')

@section('content')
<div class="container pt-4 pb-5">
    
    {{-- ALERT SALDONE: Card besar menampilkan saldo terkini --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body p-4 text-center">
                    <h6 class="text-uppercase text-muted fw-bold ls-1 mb-2">Saldo Kas Kelas Saat Ini</h6>
                    <h1 class="display-4 fw-bold text-dark mb-0">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Success: Muncul setelah submit data --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- GRID INPUT FORM (Pemasukan & Pengeluaran) --}}
    <div class="row mb-4 g-4">
        {{-- CARD KIRI: Input Pemasukan SK --}}
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-success"><i class="bi bi-arrow-down-circle-fill me-2"></i>INPUT PEMASUKAN</span>
                    
                    {{-- Tombol Setting Nominal/Tipe Kas --}}
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#settingsModal" title="Atur Nominal Kas">
                        <i class="bi bi-gear-fill me-1"></i> Atur Kas
                    </button>
                </div>

                <div class="card-body">
                    {{-- Form Pemasukan --}}
                    <form action="{{ route('admin.transactions.store.in') }}" method="POST">
                        @csrf
                        {{-- Select Siswa --}}
                        <div class="mb-3">
                            <label class="form-label small text-muted">Pilih Siswa</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">-- Cari Nama Siswa --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->nomor_absen }}. {{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            {{-- Input Jumlah (Auto fill nominal wajib) --}}
                            <div class="col-6">
                                <label class="form-label small text-muted">Jumlah (Rp)</label>
                                <input type="text" class="form-control input-rupiah" name="amount" value="{{ number_format($class->tagihan_nominal, 0, ',', '.') }}" required>
                            </div>
                            {{-- Input Tanggal --}}
                            <div class="col-6">
                                <label class="form-label small text-muted">Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        {{-- Input Keterangan --}}
                        <div class="mb-4">
                            <label class="form-label small text-muted">Keterangan</label>
                            <input type="text" class="form-control" name="description" placeholder="Contoh: Kas Minggu 1" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">Simpan Pemasukan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CARD KANAN: Input Pengeluaran --}}
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 text-danger">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>INPUT PENGELUARAN
                </div>
                <div class="card-body">
                    {{-- Form Pengeluaran --}}
                    <form action="{{ route('admin.transactions.store.out') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted">Keterangan Pengeluaran</label>
                            <input type="text" class="form-control" name="description" placeholder="Contoh: Beli Spidol" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small text-muted">Jumlah (Rp)</label>
                                <input type="text" class="form-control input-rupiah" name="amount" placeholder="15.000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        {{-- Upload Bukti Foto --}}
                        <div class="mb-4">
                            <label class="form-label small text-muted">Bukti (Foto/Video)</label>
                            <input type="file" class="form-control" name="proof_image" accept="image/*,video/*">
                            <div class="form-text small">Bisa upload foto (.jpg, .png) atau video (.mp4). Max 20MB.</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 fw-bold">Simpan Pengeluaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD DAFTAR RIWAYAT TRANSAKSI --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</span>
                    <small class="text-muted fst-italic small">Klik baris untuk detail</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="transactionsTableWrap" style="max-height: 500px; overflow-y: auto; position: relative;">
                        <!-- Toolbar Pilihan (Disembunyikan sampai mode pilih aktif) -->
                        <div id="selectionToolbar" class="d-none position-absolute" style="top:12px; right:28px; z-index:70;">
                            <div style="display:flex; gap:8px; align-items:center; padding:6px 8px; background: rgba(255,255,255,0.98); border-radius:8px; box-shadow: 0 8px 20px rgba(16,24,40,0.06);">
                                <button id="bulkDeleteBtn" class="btn btn-danger btn-sm">Hapus Terpilih</button>
                                <button id="cancelSelectionBtn" class="btn btn-secondary btn-sm">Batal</button>
                            </div>
                        </div>

                        <style>
                        /* Styling untuk checkbox seleksi */
                        #transactionsTableWrap .row-select { display: none; }
                        #transactionsTableWrap.selection-mode .row-select { display: inline-block; }
                        #transactionsTableWrap.selection-mode tbody tr { cursor: pointer; }
                        #transactionsTableWrap .row-checkbox-cell { width: 48px; }

                        /* Toolbar tweaks agar tidak menabrak header */
                        #selectionToolbar > div > .btn { white-space: nowrap; }
                        @media (max-width: 575px) {
                            #selectionToolbar { right: 8px !important; top: 8px !important; }
                        }
                        </style>

                        {{-- Tabel Transaksi --}}
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light sticky-top" style="top: 0; z-index: 5;">
                                <tr>
                                    <th class="ps-4 row-checkbox-cell"></th>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-4">Jumlah</th>
                                </tr>
                            </thead>
                           <tbody>
                                @forelse($latestTransactions as $transaction)
                                    {{-- Baris Transaksi --}}
                                    <tr style="cursor: pointer;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal"
                                        data-date="{{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') }}"
                                        data-type="{{ $transaction->type }}"
                                        data-desc="{{ $transaction->description }}"
                                        data-amount="Rp {{ number_format($transaction->amount, 0, ',', '.') }}"
                                        data-who="{{ $transaction->type == 'masuk' ? ($transaction->student->name ?? '-') : 'Keperluan Kelas' }}"
                                        data-img="{{ $transaction->proof_image ? asset('storage/' . $transaction->proof_image) : '' }}"
                                        data-delete="{{ route('admin.transactions.destroy', $transaction->id) }}">

                                        <td class="row-checkbox-cell ps-4">
                                            {{-- Checkbox untuk seleksi massal --}}
                                            <input type="checkbox" class="row-select form-check-input" value="{{ $transaction->id }}" aria-label="Pilih transaksi {{ $transaction->id }}">
                                        </td>

                                        <td class="ps-4 text-muted small text-nowrap">
                                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                                        </td>
                                        <td>
                                            @if($transaction->type == 'masuk')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                    <i class="bi bi-arrow-down"></i> Masuk
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                    <i class="bi bi-arrow-up"></i> Keluar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-dark text-truncate" style="max-width: 250px;">
                                            <span class="fw-bold d-block" style="font-size: 1rem;">{{ $transaction->description }}</span>
                                            
                                            {{-- Sub-teks (Nama Siswa atau Keperluan Kelas) --}}
                                            <div class="mt-1">
                                                @if($transaction->type == 'masuk' && $transaction->student)
                                                    <span style="color: #4A7AB3 !important; font-weight: 800; font-size: 0.9rem;">
                                                        <i class="bi bi-person-fill me-1"></i>{{ $transaction->student->name }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary fw-bold" style="font-size: 0.85rem;">
                                                        <i class="bi bi-shop me-1"></i>Keperluan Kelas
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-nowrap pe-4">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi.</td>
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

{{-- MODAL #1: KONFIRMASI HAPUS MASAL (Bulk Delete) --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="bulkDeleteMessage">Anda akan menghapus <strong id="bulkDeleteCount">0</strong> transaksi. Tindakan ini tidak dapat dibatalkan.</p>

                <div id="bulkDeleteListWrap" style="max-height:220px; overflow:auto;">
                    <ul id="bulkDeleteList" class="list-group list-group-flush">
                        <!-- List item akan diisi via Javascript -->
                    </ul>
                </div>

                <p class="text-muted small mt-3">Pilih "Ya, Hapus" untuk melanjutkan atau "Batal" untuk kembali.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="bulkDeleteConfirmBtn" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL #2: PENGATURAN KAS (Atur Nominal & Periode Tagihan) --}}
<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Pengaturan Kas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 pt-0">
                    <div class="alert alert-light border text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Pengaturan ini hanya berlaku untuk kelas <strong>{{ $class->name }}</strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nominal Tagihan (Rp)</label>
                        <input type="text" class="form-control input-rupiah fw-bold text-dark" name="tagihan_nominal" value="{{ number_format($class->tagihan_nominal, 0, ',', '.') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Periode Penagihan</label>
                        <select class="form-select" name="tagihan_tipe" required>
                            <option value="harian" {{ $class->tagihan_tipe == 'harian' ? 'selected' : '' }}>Harian (Setiap Hari)</option>
                            <option value="mingguan" {{ $class->tagihan_tipe == 'mingguan' ? 'selected' : '' }}>Mingguan (1x Seminggu)</option>
                            <option value="bulanan" {{ $class->tagihan_tipe == 'bulanan' ? 'selected' : '' }}>Bulanan (1x Sebulan)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL #3: DETAIL TRANSAKSI --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <h5 class="modal-title fw-bold">Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="text-center py-4 bg-light border-bottom">
                    <span id="modalTypeBadge" class="badge rounded-pill px-3 py-2 mb-2">TYPE</span>
                    <h2 class="fw-bold text-dark mb-0" id="modalAmount">Rp 0</h2>
                    <small class="text-muted" id="modalDate">Tanggal</small>
                </div>
                
                <div class="p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="small text-muted fw-bold text-uppercase ls-1 mb-1">Keterangan</label>
                        <p class="fs-5 text-dark mb-0" id="modalDesc">-</p>
                    </div>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="small text-muted fw-bold text-uppercase ls-1 mb-1">Oleh / Untuk</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-light rounded-circle p-2 text-primary"><i class="bi bi-person-fill"></i></div>
                            <span class="fw-bold text-dark" id="modalWho">-</span>
                        </div>
                    </div>

                    {{-- Area Bukti Foto (Hidden jika kosong) --}}
                    <div id="modalProofArea" class="d-none">
                        <label class="small text-muted fw-bold text-uppercase ls-1 mb-2">Bukti Transaksi</label>
                        <button class="btn p-0 w-100 border rounded-3 overflow-hidden position-relative shadow-sm" 
                                data-bs-target="#fullMediaModal" data-bs-toggle="modal">
                            {{-- Preview Bukti --}}
                            <div class="ratio ratio-16x9 bg-dark d-flex align-items-center justify-content-center">
                                <img id="modalImg" src="" class="w-100 h-100 object-fit-cover d-none">
                                <div id="modalVideoIcon" class="text-white d-none">
                                    <i class="bi bi-play-circle display-1"></i>
                                    <p class="mt-2 mb-0 small">Klik untuk memutar video</p>
                                </div>
                            </div>
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                 style="background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s;"
                                 onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                <span class="badge bg-dark"><i class="bi bi-zoom-in me-1"></i> Lihat Full</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 p-3">
                {{-- Form Delete (Hapus) --}}
                <form id="modalDeleteForm" action="" method="POST" onsubmit="return confirm('Yakin hapus data ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">Hapus Data</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL #4: FULL SIZE PROOF PREVIEW (Gede) --}}
<div class="modal fade" id="fullMediaModal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                
                <div class="bg-black rounded-4 overflow-hidden d-flex align-items-center justify-content-center position-relative" 
                     style="height: 85vh; width: 100%;">
                    
                    {{-- Full Image Element --}}
                    <img id="fullImageSrc" src="" class="d-none" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    
                    {{-- Full Video Element --}}
                    <video id="fullVideoSrc" controls class="d-none" style="max-width: 100%; max-height: 100%;">
                        <source src="" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>

                </div>
                
                <div class="mt-3">
                    <button class="btn btn-light rounded-pill fw-bold px-4 shadow" data-bs-target="#detailModal" data-bs-toggle="modal">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- LOGIKA MODAL DETAIL & MEDIA PREVIEW ---
    var detailModal = document.getElementById('detailModal');
    var fullMediaModal = document.getElementById('fullMediaModal');

    // Event saat modal detail dibuka
    detailModal.addEventListener('show.bs.modal', function (event) {
        // Ambil data dari tombol pemicu (row tabel)
        var trigger = event.relatedTarget;
        var tr = trigger.closest('tr'); 
        
        // Ambil atribut data-*
        var type = tr.getAttribute('data-type');
        var amount = tr.getAttribute('data-amount');
        var date = tr.getAttribute('data-date');
        var desc = tr.getAttribute('data-desc');
        var who = tr.getAttribute('data-who');
        var imgUrl = tr.getAttribute('data-img');
        var deleteUrl = tr.getAttribute('data-delete');

        // Isi konten modal
        document.getElementById('modalAmount').textContent = amount;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalDesc').textContent = desc;
        document.getElementById('modalWho').textContent = who;
        document.getElementById('modalDeleteForm').action = deleteUrl;

        // Atur badge tipe (Masuk/Keluar)
        var badge = document.getElementById('modalTypeBadge');
        if (type === 'masuk') {
            badge.className = 'badge bg-success rounded-pill px-3 py-2 mb-2';
            badge.innerHTML = '<i class="bi bi-arrow-down me-1"></i> Pemasukan';
        } else {
            badge.className = 'badge bg-danger rounded-pill px-3 py-2 mb-2';
            badge.innerHTML = '<i class="bi bi-arrow-up me-1"></i> Pengeluaran';
        }

        // Logic Preview Media (Foto/Video)
        var proofArea = document.getElementById('modalProofArea');
        var previewImg = document.getElementById('modalImg');
        var previewVideoIcon = document.getElementById('modalVideoIcon');
        var fullImg = document.getElementById('fullImageSrc');
        var fullVideo = document.getElementById('fullVideoSrc');

        if (imgUrl) {
            proofArea.classList.remove('d-none');
            // Cek ekstensi file untuk menentukan video/gambar
            var extension = imgUrl.split('.').pop().toLowerCase();
            var isVideo = ['mp4', 'mov', 'avi', 'mkv'].includes(extension);

            if (isVideo) {
                previewImg.classList.add('d-none');
                previewVideoIcon.classList.remove('d-none');
                fullImg.classList.add('d-none');
                fullVideo.classList.remove('d-none');
                fullVideo.src = imgUrl; // Set source video
            } else {
                previewImg.src = imgUrl;
                previewImg.classList.remove('d-none');
                previewVideoIcon.classList.add('d-none');
                fullVideo.classList.add('d-none');
                fullImg.classList.remove('d-none');
                fullImg.src = imgUrl; // Set source gambar
            }
        } else {
            proofArea.classList.add('d-none');
        }
    });

    // Reset video saat modal full ditutup agar suara tidak bocor
    fullMediaModal.addEventListener('hide.bs.modal', function () {
        var video = document.getElementById('fullVideoSrc');
        video.pause();
        video.currentTime = 0;
    });

    // --- LOGIKA MULTI SELECT (KLIK KANAN) & HAPUS MASSAL ---
    (function(){
        const tableWrap = document.getElementById('transactionsTableWrap');
        if (!tableWrap) return;
        const tbody = tableWrap.querySelector('tbody');
        const toolbar = document.getElementById('selectionToolbar');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const cancelBtn = document.getElementById('cancelSelectionBtn');
        const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        // Masuk mode seleksi
        function enterSelectionMode() {
            tableWrap.classList.add('selection-mode');
            toolbar.classList.remove('d-none');
        }

        // Keluar mode seleksi
        function exitSelectionMode() {
            tableWrap.classList.remove('selection-mode');
            toolbar.classList.add('d-none');
            // Uncheck semua
            tableWrap.querySelectorAll('.row-select').forEach(cb => cb.checked = false);
        }

        // Event: Klik Kanan pada baris
        tbody.addEventListener('contextmenu', function(e){
            const tr = e.target.closest('tr');
            if (!tr) return;
            e.preventDefault(); // Cegah menu browser
            enterSelectionMode();
            const cb = tr.querySelector('.row-select');
            if (cb) cb.checked = true; // Auto select baris yang diklik kanan
        });

        // Event: Klik Kiri saat mode seleksi aktif (Toggle Checkbox)
        tbody.addEventListener('click', function(e){
            if (!tableWrap.classList.contains('selection-mode')) return;
            const tr = e.target.closest('tr');
            if (!tr) return;
            // Cegah event modal (biar gak muncul modal detail)
            const cb = tr.querySelector('.row-select');
            if (!cb) return;
            
            cb.checked = !cb.checked;
            e.stopPropagation(); // Stop event bubbling
        }, true); // Use capture phase

        // --- BULK DELETE LOGIC ---
        const bulkDeleteModalEl = document.getElementById('bulkDeleteModal');
        const bulkDeleteModal = bulkDeleteModalEl ? new bootstrap.Modal(bulkDeleteModalEl) : null;
        const bulkDeleteConfirmBtn = document.getElementById('bulkDeleteConfirmBtn');
        const bulkDeleteCountEl = document.getElementById('bulkDeleteCount');

        function gatherSelectedRows() {
            // Ambil semua tr yang checkboxnya dicentang
            return Array.from(tableWrap.querySelectorAll('.row-select:checked')).map(cb => cb.closest('tr'));
        }

        // Helper untuk tampilkan pesan toast kecil
        function showInlineToast(message, type) {
            const existing = document.getElementById('inlineToast');
            if (existing) existing.remove();
            const wrap = document.createElement('div');
            wrap.id = 'inlineToast';
            wrap.style.position = 'absolute';
            wrap.style.top = '12px';
            wrap.style.left = '12px';
            wrap.style.zIndex = 90;
            wrap.innerHTML = `<div class="alert alert-${type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'success')} border-0 shadow-sm mb-0">${message}</div>`;
            tableWrap.appendChild(wrap);
            setTimeout(() => { try{ wrap.remove(); }catch(e){} }, 2800);
        }

        // Klik tombol Hapus Terpilih
        bulkDeleteBtn.addEventListener('click', function(){
            const rows = gatherSelectedRows();
            if (rows.length === 0) { showInlineToast('Pilih transaksi terlebih dahulu.', 'warning'); return; }

            // Isi list di dalam modal konfirmasi
            const listEl = document.getElementById('bulkDeleteList');
            if (listEl) {
                listEl.innerHTML = '';
                const maxShow = 50; // Batas tampil list agar tidak berat
                rows.slice(0, maxShow).forEach(tr => {
                    const date = tr.getAttribute('data-date') || '';
                    const desc = tr.getAttribute('data-desc') || tr.querySelector('td:nth-child(4)')?.innerText || '';
                    const amount = tr.getAttribute('data-amount') || tr.querySelector('td:last-child')?.innerText || '';
                    
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-start small';
                    li.innerHTML = `<div><strong class="text-dark">${desc}</strong><div class="text-muted small">${date}</div></div><div class="fw-bold text-primary ms-3">${amount}</div>`;
                    listEl.appendChild(li);
                });
                
                // Jika lebih dari 50, beri info
                if (rows.length > maxShow) {
                    const moreLi = document.createElement('li');
                    moreLi.className = 'list-group-item small text-muted text-center';
                    moreLi.innerText = `+ ${rows.length - maxShow} lainnya...`;
                    listEl.appendChild(moreLi);
                }
            }

            if (bulkDeleteCountEl) bulkDeleteCountEl.textContent = rows.length;
            if (bulkDeleteModal) bulkDeleteModal.show();
        });

        // Klik Ya Hapus di Modal
        if (bulkDeleteConfirmBtn) {
            bulkDeleteConfirmBtn.addEventListener('click', function(){
                const rows = gatherSelectedRows();
                if (rows.length === 0) { if (bulkDeleteModal) bulkDeleteModal.hide(); return; }
                
                // UI Loading state
                bulkDeleteConfirmBtn.disabled = true;
                bulkDeleteConfirmBtn.innerHTML = 'Menghapus...';

                // Buat array promise fetch delete untuk setiap row
                const promises = rows.map(tr => {
                    const url = tr.getAttribute('data-delete');
                    return fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    }).then(resp => ({ resp, tr }));
                });

                // Eksekusi semua hapus paralell
                Promise.all(promises).then(results => {
                    let successCount = 0;
                    results.forEach(r => {
                        if (r.resp.ok) { r.tr.remove(); successCount++; } // Hapus dari UI jika sukses
                    });
                    
                    exitSelectionMode();
                    if (bulkDeleteModal) bulkDeleteModal.hide();
                    showInlineToast(successCount + ' transaksi berhasil dihapus.', 'success');
                }).catch(err => {
                    console.error(err);
                    showInlineToast('Terjadi kesalahan saat menghapus. Coba lagi.', 'danger');
                }).finally(() => {
                    // Reset tombol modal
                    bulkDeleteConfirmBtn.disabled = false;
                    bulkDeleteConfirmBtn.innerHTML = 'Ya, Hapus';
                });
            });
        }

        // Tombol Batal
        cancelBtn.addEventListener('click', function(){ exitSelectionMode(); });

        // Tombol Escape Keyboard
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') exitSelectionMode(); });
    })();
</script>
@endpush