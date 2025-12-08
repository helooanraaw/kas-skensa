@extends('layouts.app')

@section('content')

{{-- DASHBOARD ADMIN CONTAINER --}}
<div class="container pt-4 pb-5">
    
    {{-- HEADER DASHBOARD & FILTER --}}
    <div class="row mb-4 align-items-end">
        {{-- Judul Halaman & Salam --}}
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h4 class="fw-bold text-dark mb-1">Dashboard Kas Kelas</h4>
            <p class="text-muted small mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
        
        {{-- Form Filter Periode (Bulan & Tahun) --}}
        <div class="col-12 col-md-6">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="d-flex flex-wrap align-items-center gap-3 justify-content-md-end">
                    
                    {{-- Dropdown Bulan --}}
                    <div class="position-relative">
                        <select name="bulan" class="form-select shadow-sm border-0" style="min-width: 150px; cursor: pointer;">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedBulan == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Dropdown Tahun --}}
                    <div class="position-relative">
                        <select name="tahun" class="form-select shadow-sm border-0" style="min-width: 100px; cursor: pointer;">
                            @for($i = date('Y'); $i >= 2024; $i--)
                                <option value="{{ $i }}" {{ $selectedTahun == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tombol Filter --}}
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    
                    {{-- Tombol Export Excel --}}
                    <a href="{{ route('admin.export.excel', ['bulan' => $selectedBulan, 'tahun' => $selectedTahun]) }}" class="btn btn-success px-4 shadow-sm fw-bold">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export
                    </a>

                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY CARDS ROW --}}
    <div class="row g-3 mb-4"> 
        {{-- Card 1: Total Saldo Keseluruhan --}}
        <div class="col-12 col-md-4">
            <div class="card text-white bg-primary mb-3 h-100 border-0 shadow" style="background-color: var(--skensa-dark-blue) !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Total Saldo Kas (Seluruh Waktu)</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        {{-- Card 2: Pemasukan Bulan Ini --}}
        <div class="col-12 col-md-4">
            <div class="card text-white bg-success mb-3 h-100 border-0 shadow" style="background-color: var(--skensa-teal) !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Pemasukan ({{ DateTime::createFromFormat('!m', $selectedBulan)->format('F') }})</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        {{-- Card 3: Pengeluaran Bulan Ini --}}
        <div class="col-12 col-md-4">
            <div class="card text-white bg-danger mb-3 h-100 border-0 shadow" style="background-color: #DC3545 !important;">
                <div class="card-header border-0 text-center bg-transparent pt-4 pb-0">
                    <small class="text-uppercase ls-1 opacity-75">Pengeluaran ({{ DateTime::createFromFormat('!m', $selectedBulan)->format('F') }})</small>
                </div>
                <div class="card-body text-center pt-2 pb-4">
                    <h2 class="card-title display-6 fw-bold mb-0">Rp {{ number_format($totalKeluarPeriode, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 1 (Donut & Bar) --}}
    <div class="row g-4 mb-4">
        {{-- Chart Donut: Proporsi --}}
        <div class="col-12 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-pie-chart me-2"></i>Proporsi Pemasukan & Pengeluaran
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="statusSiswaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Chart Bar: Perbandingan Nominal --}}
        <div class="col-12 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-bar-chart me-2"></i>Akumulasi Bulan Ini
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="akumulasiKasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART ROW 2 (Line Chart Area) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-graph-up-arrow me-2"></i>Progres Kas Harian
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
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
    // --- AMBIL DATA DARI CONTROLLER & PAKSA JADI INT AGAR AMAN ---
    const siswaLunas = parseInt("{{ $siswaLunas ?? 0 }}");
    const siswaNunggak = parseInt("{{ $siswaNunggak ?? 0 }}");
    
    // Data total nominal
    const totalMasuk = parseInt("{{ $totalMasukPeriode ?? 0 }}");
    const totalKeluar = parseInt("{{ $totalKeluarPeriode ?? 0 }}");
    
    // Data harian untuk chart area
    const datesLabel = @json($dates);
    const dataPemasukan = @json($pemasukanPerHari);
    const dataPengeluaran = @json($pengeluaranPerHari);

    // 1. GRAFIK DONAT (PROPORSI PEMASUKAN VS PENGELUARAN)
    const ctxDonat = document.getElementById('statusSiswaChart');
    if (ctxDonat) {
        // [UBAH DATA] User ingin grafik akurat sesuai nominal uang (bukan jumlah siswa)
        let donutData = [totalMasuk, totalKeluar]; 
        let donutColors = ['#0d6efd', '#DC3545']; // Biru (Masuk), Merah (Keluar)
        let donutLabels = ['Pemasukan', 'Pengeluaran'];
        
        // Hitung Total Nominal
        let totalNominal = totalMasuk + totalKeluar;

        // Jika KOSONG (0), Tampilkan placeholder abu-abu
        if (totalNominal === 0) {
            donutData = [1]; 
            donutColors = ['#E2E8F0']; 
            donutLabels = ['Belum ada data'];
        }

        new Chart(ctxDonat, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderWidth: 0,
                    hoverOffset: totalNominal === 0 ? 0 : 4
                }]
            },
            options: { 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { 
                        position: 'right',
                        display: totalNominal > 0 
                    },
                    tooltip: { 
                        enabled: totalNominal > 0,
                        callbacks: {
                            // Custom format tooltip Rupiah
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                layout: { padding: 10 },
                cutout: '60%'
            }
        });
    }

    // 2. GRAFIK BATANG (PERBANDINGAN TOTAL)
    const ctxBatang = document.getElementById('akumulasiKasChart');
    if (ctxBatang) {
        // Logika agar skala y-axis terlihat rapi
        const maxVal = Math.max(totalMasuk, totalKeluar, 1);
        let step = 50000;
        if (maxVal > 500000) step = 100000;
        const niceMax = Math.ceil(maxVal / step) * step;

        new Chart(ctxBatang, {
            type: 'bar',
            data: {
                labels: ['Masuk', 'Keluar'],
                datasets: [{
                    label: 'Rupiah',
                    data: [totalMasuk, totalKeluar],
                    backgroundColor: ['#0d6efd', '#DC3545'],
                    borderRadius: 6,
                    barThickness: 70,
                    maxBarThickness: 60,
                    barPercentage: 0.6,
                    categoryPercentage: 0.6
                }]
            },
            options: { 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        max: niceMax,
                        ticks: { 
                            callback: function(v){ return 'Rp ' + v.toLocaleString('id-ID'); } 
                        },
                        grid: { color: '#f1f5f9' }
                    }, 
                    x: { grid: { display: false }, border: { display: false } } 
                } 
            }
        });
    }

    // 3. GRAFIK AREA (GUNUNG) Pemasukan vs Pengeluaran Harian
    new Chart(document.getElementById('progresAreaChart'), {
        type: 'line',
        data: {
            labels: datesLabel,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: dataPemasukan,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                },
                {
                    label: 'Pengeluaran',
                    data: dataPengeluaran,
                    borderColor: '#DC3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f0f0f0' },
                        ticks: { 
                            callback: function(value) { 
                                return 'Rp ' + value.toLocaleString('id-ID'); 
                            } 
                        }
                    },
                   x: { 
                        grid: { 
                            display: false 
                        }, 
                        ticks: { 
                            display: true,      // ✅ WAJIB TRUE: Agar tanggal muncul
                            autoSkip: true,     // ✅ TRUE: Agar otomatis loncat kalau sempit
                            maxTicksLimit: 30,  // ✅ Tampilkan hingga 30 titik
                            maxRotation: 0,     // ✅ Teks lurus
                            minRotation: 0
                        } 
                    }
                },
                plugins: { 
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
    });
</script>
@endpush