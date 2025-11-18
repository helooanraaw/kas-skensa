@extends('layouts.app')

@section('content')
<div class="container pt-4 pb-5">
    
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

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row mb-4 g-4">
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 text-success">
                    <i class="bi bi-arrow-down-circle-fill me-2"></i>INPUT PEMASUKAN
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.transactions.store.in') }}" method="POST">
                        @csrf
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
                            <div class="col-6">
                                <label class="form-label small text-muted">Jumlah (Rp)</label>
                                <input type="number" class="form-control" name="amount" placeholder="5000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted">Keterangan</label>
                            <input type="text" class="form-control" name="description" placeholder="Contoh: Kas Minggu 1" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">Simpan Pemasukan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 text-danger">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>INPUT PENGELUARAN
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.transactions.store.out') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted">Keterangan Pengeluaran</label>
                            <input type="text" class="form-control" name="description" placeholder="Contoh: Beli Spidol" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small text-muted">Jumlah (Rp)</label>
                                <input type="number" class="form-control" name="amount" placeholder="15000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted">Bukti (Foto/Video)</label>
                            <input type="file" class="form-control" name="proof_image" accept="image/*,video/*">
                            <div class="form-text small">Max 20MB.</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 fw-bold">Simpan Pengeluaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</span>
                    <small class="text-muted fst-italic small">Klik pada baris untuk lihat detail</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light sticky-top" style="top: 0; z-index: 5;">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-4">Jumlah</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTransactions as $transaction)
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
                                        <td class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                            {{ $transaction->description }}
                                            <div class="small text-muted fw-normal">
                                                @if($transaction->type == 'masuk' && $transaction->student)
                                                    Oleh: {{ $transaction->student->name }}
                                                @else
                                                    Keperluan Kelas
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-nowrap pe-4">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada transaksi.</td>
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

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white p-4 position-relative">
                <h5 class="modal-title fw-bold position-relative z-1">Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white position-relative z-1" data-bs-dismiss="modal" aria-label="Close"></button>
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

                    <div id="modalProofArea" class="d-none">
                        <label class="small text-muted fw-bold text-uppercase ls-1 mb-2">Bukti Transaksi</label>
                        <button class="btn p-0 w-100 border rounded-3 overflow-hidden position-relative shadow-sm" 
                                data-bs-target="#fullMediaModal" data-bs-toggle="modal">
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

<div class="modal fade" id="fullMediaModal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                
                <div class="bg-black rounded-4 overflow-hidden d-flex align-items-center justify-content-center position-relative" 
                     style="height: 85vh; width: 100%;">
                    
                    <img id="fullImageSrc" src="" class="d-none" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    
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
    var detailModal = document.getElementById('detailModal');
    var fullMediaModal = document.getElementById('fullMediaModal');

    detailModal.addEventListener('show.bs.modal', function (event) {
        // Mendeteksi elemen pemicu (bisa TR atau BUTTON)
        var trigger = event.relatedTarget;
        // Jika klik terjadi di dalam TR, pastikan kita ambil TR-nya
        var tr = trigger.closest('tr'); 
        
        // Ambil data dari atribut TR
        var type = tr.getAttribute('data-type');
        var amount = tr.getAttribute('data-amount');
        var date = tr.getAttribute('data-date');
        var desc = tr.getAttribute('data-desc');
        var who = tr.getAttribute('data-who');
        var imgUrl = tr.getAttribute('data-img');
        var deleteUrl = tr.getAttribute('data-delete');

        // Isi Modal
        document.getElementById('modalAmount').textContent = amount;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalDesc').textContent = desc;
        document.getElementById('modalWho').textContent = who;
        document.getElementById('modalDeleteForm').action = deleteUrl;

        // Badge Warna
        var badge = document.getElementById('modalTypeBadge');
        if (type === 'masuk') {
            badge.className = 'badge bg-success rounded-pill px-3 py-2 mb-2';
            badge.innerHTML = '<i class="bi bi-arrow-down me-1"></i> Pemasukan';
        } else {
            badge.className = 'badge bg-danger rounded-pill px-3 py-2 mb-2';
            badge.innerHTML = '<i class="bi bi-arrow-up me-1"></i> Pengeluaran';
        }

        // Media Logic (Foto/Video)
        var proofArea = document.getElementById('modalProofArea');
        var previewImg = document.getElementById('modalImg');
        var previewVideoIcon = document.getElementById('modalVideoIcon');
        var fullImg = document.getElementById('fullImageSrc');
        var fullVideo = document.getElementById('fullVideoSrc');

        if (imgUrl) {
            proofArea.classList.remove('d-none');
            var extension = imgUrl.split('.').pop().toLowerCase();
            var isVideo = ['mp4', 'mov', 'avi', 'mkv'].includes(extension);

            if (isVideo) {
                previewImg.classList.add('d-none');
                previewVideoIcon.classList.remove('d-none');
                fullImg.classList.add('d-none');
                fullVideo.classList.remove('d-none');
                fullVideo.src = imgUrl;
            } else {
                previewImg.src = imgUrl;
                previewImg.classList.remove('d-none');
                previewVideoIcon.classList.add('d-none');
                fullVideo.classList.add('d-none');
                fullImg.classList.remove('d-none');
                fullImg.src = imgUrl;
            }
        } else {
            proofArea.classList.add('d-none');
        }
    });

    fullMediaModal.addEventListener('hide.bs.modal', function () {
        var video = document.getElementById('fullVideoSrc');
        video.pause();
        video.currentTime = 0;
    });
</script>
@endpush