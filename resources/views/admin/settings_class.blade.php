@extends('layouts.app')

{{-- SECTION CONTENT: Halaman Pengaturan Kelas (Nominal & Periode) --}}
@section('content')
<div class="container pt-4 pb-5">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4">
                {{-- Header Card --}}
                <div class="card-header bg-white border-bottom p-4">
                    <h4 class="fw-bold text-dark mb-0"><i class="bi bi-sliders me-2 text-primary"></i>Pengaturan Kas Kelas</h4>
                    <p class="text-muted small mb-0">Sesuaikan nominal dan periode tagihan untuk kelas <strong>{{ $class->name }}</strong>.</p>
                </div>
                
                <div class="card-body p-4">
                    {{-- Alert Info Penting --}}
                    <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Penting:</strong><br>
                            Mengubah pengaturan ini akan otomatis menghitung ulang status "Lunas/Nunggak" seluruh siswa di halaman publik.
                        </div>
                    </div>

                    {{-- Form Update Pengaturan --}}
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Input Nominal Tagihan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Nominal Tagihan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                                    <input type="number" class="form-control border-start-0 ps-0" name="tagihan_nominal" value="{{ $class->tagihan_nominal }}" required>
                                </div>
                                <div class="form-text">Contoh: 2000, 5000, 10000</div>
                            </div>

                            {{-- Input Periode Tagihan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Periode Penagihan</label>
                                <select class="form-select" name="tagihan_tipe" required>
                                    <option value="harian" {{ $class->tagihan_tipe == 'harian' ? 'selected' : '' }}>Harian (Setiap Hari)</option>
                                    <option value="mingguan" {{ $class->tagihan_tipe == 'mingguan' ? 'selected' : '' }}>Mingguan (1x Seminggu)</option>
                                    <option value="bulanan" {{ $class->tagihan_tipe == 'bulanan' ? 'selected' : '' }}>Bulanan (1x Sebulan)</option>
                                </select>
                                <div class="form-text">Seberapa sering siswa wajib bayar?</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-light text-muted">Kembali</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Simpan Perubahan</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection