@extends('layouts.app')

@section('content')
<div class="container pt-4 pb-5">
    
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-4">
            <div class="card text-white bg-primary mb-3 h-100 border-0 shadow" style="background-color: var(--skensa-dark-blue) !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Total Saldo Kas</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-white bg-success mb-3 h-100 border-0 shadow" style="background-color: var(--skensa-teal) !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Pemasukan</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-white bg-danger mb-3 h-100 border-0 shadow" style="background-color: #DC3545 !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Pengeluaran</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($totalKeluarPeriode, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-uppercase text-muted small ls-1">Aksi Cepat</div>
                <div class="card-body d-flex gap-3 p-4 flex-wrap"> 
                    <a href="{{ route('admin.students.index') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="bi bi-people-fill me-2"></i> Manajemen Siswa
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-success btn-lg px-4 shadow-sm">
                        <i class="bi bi-cash-stack me-2"></i> Input Transaksi
                    </a>
                    <a href="{{ route('admin.export.excel', ['bulan' => $selectedBulan, 'tahun' => $selectedTahun]) }}" class="btn btn-outline-primary btn-lg px-4">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-12 col-lg-6"> 
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0">Status Pembayaran Siswa</div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="statusSiswaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0">Akumulasi Bulan Ini</div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="akumulasiKasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0">Progres Kas (30 Hari Terakhir)</div>
                <div class="card-body">
                    <div style="height: 350px; width: 100%;">
                        <canvas id="progresAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // --- 1. PERSIAPAN DATA DARI CONTROLLER ---
    const siswaLunas = {{ $siswaLunas }};
    const siswaNunggak = {{ $siswaNunggak }};
    // PENTING: Ganti variabel JS agar sesuai dengan filter
    const totalMasuk = {{ $totalMasukPeriode }};
    const totalKeluar = {{ $totalKeluarPeriode }};
    
    // Data Array untuk Grafik Area
    const datesLabel = @json($dates);
    const dataPemasukan = @json($pemasukanPerHari);
    const dataPengeluaran = @json($pengeluaranPerHari);

    // --- 2. RENDER GRAFIK ---

    // A. Grafik Donat
    const ctxDonat = document.getElementById('statusSiswaChart');
    if (ctxDonat) {
        new Chart(ctxDonat, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Nunggak'],
                datasets: [{
                    data: [siswaLunas, siswaNunggak],
                    backgroundColor: ['#2AA5A5', '#DC3545'],
                    borderWidth: 0
                }]
            },
            options: { maintainAspectRatio: false }
        });
    }

    // B. Grafik Batang (Data sudah difilter)
    const ctxBatang = document.getElementById('akumulasiKasChart');
    if (ctxBatang) {
        new Chart(ctxBatang, {
            type: 'bar',
            data: {
                labels: ['Masuk', 'Keluar'],
                datasets: [{
                    label: 'Rupiah',
                    data: [totalMasuk, totalKeluar],
                    backgroundColor: ['#2AA5A5', '#DC3545'],
                    borderRadius: 8
                }]
            },
            options: { 
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // C. Grafik Area (Gunung) (Data sudah difilter)
    const ctxArea = document.getElementById('progresAreaChart');
    if (ctxArea) {
        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: datesLabel,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: dataPemasukan,
                        borderColor: '#2AA5A5',
                        backgroundColor: 'rgba(42, 165, 165, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Pengeluaran',
                        data: dataPengeluaran,
                        borderColor: '#DC3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false, },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                },
                plugins: { tooltip: { mode: 'index', intersect: false } }
            }
        });
    }
</script>
@endpush