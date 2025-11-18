@extends('layouts.app')

@section('content')
<div class="container pt-4 pb-5">
    
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 rounded-pill border border-primary border-opacity-10">TRANSPARANSI KAS</span>
            <h2 class="fw-bold text-dark mb-0">{{ $class->name }}</h2>
            <p class="text-muted small mb-0">Data keuangan real-time kelas {{ $class->name }}</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-block text-start bg-white border rounded-3 p-3 shadow-sm">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Target Wajib</small>
                <span class="fw-bold text-dark">Rp {{ number_format($totalWajibBayar, 0, ',', '.') }}</span>
                <small class="text-muted">/siswa</small>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded text-primary me-3"><i class="bi bi-wallet2 fs-4"></i></div>
                        <h6 class="fw-bold text-muted mb-0 text-uppercase ls-1">Saldo Kas</h6>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-2 rounded text-info me-3"><i class="bi bi-tag fs-4"></i></div>
                        <h6 class="fw-bold text-muted mb-0 text-uppercase ls-1">Info Tagihan</h6>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Rp {{ number_format($class->tagihan_nominal, 0, ',', '.') }}</h4>
                    <small class="text-muted">Per {{ ucfirst($class->tagihan_tipe) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded text-success me-3"><i class="bi bi-people fs-4"></i></div>
                        <h6 class="fw-bold text-muted mb-0 text-uppercase ls-1">Total Siswa</h6>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ count($students) }}</h4>
                    <small class="text-muted">Siswa Terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h6 class="fw-bold mb-0 text-uppercase ls-1"><i class="bi bi-trophy me-2 text-warning"></i>Status & Peringkat</h6>
                    <form action="{{ route('kas.show', $class->slug) }}" method="GET">
                        <select name="sort" class="form-select form-select-sm border-0 bg-light fw-bold" style="width: auto; cursor: pointer;" onchange="this.form.submit()">
                            <option value="absen" {{ $currentSort == 'absen' ? 'selected' : '' }}>Urut No. Absen</option>
                            <option value="tertinggi" {{ $currentSort == 'tertinggi' ? 'selected' : '' }}>Paling Rajin (Top Rank)</option>
                            <option value="terendah" {{ $currentSort == 'terendah' ? 'selected' : '' }}>Paling Nunggak</option>
                        </select>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive" style="height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top" style="top: 0; z-index: 5;">
                                <tr>
                                    <th class="ps-4">Rank</th>
                                    <th>Siswa</th>
                                    <th>Status</th>
                                    <th>Akumulasi</th>
                                    <th class="text-end pe-4">Ket.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr class="{{ $student->rank <= 3 ? 'bg-warning bg-opacity-10' : '' }}">
                                        <td class="ps-4">
                                            @if($student->rank == 1) <span class="badge bg-warning text-dark border border-warning rounded-pill">#1</span>
                                            @elseif($student->rank == 2) <span class="badge bg-secondary text-white border border-secondary rounded-pill">#2</span>
                                            @elseif($student->rank == 3) <span class="badge text-white border border-secondary rounded-pill" style="background-color: #CD7F32;">#3</span>
                                            @else <span class="text-muted fw-bold small ms-2">#{{ $student->rank }}</span> @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold text-secondary border" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    {{ $student->nomor_absen }}
                                                </div>
                                                <span class="fw-bold text-dark small">{{ $student->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($student->tunggakan > 0) <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Nunggak</span>
                                            @else <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Lunas</span> @endif
                                        </td>
                                        <td class="fw-bold text-primary small">Rp {{ number_format($student->total_paid, 0, ',', '.') }}</td>
                                        <td class="text-end pe-4 small">
                                            @if($student->tunggakan > 0) <span class="text-danger fw-bold">-{{ number_format($student->tunggakan, 0, ',', '.') }}</span>
                                            @else <span class="text-success fw-bold">+{{ number_format(abs($student->tunggakan), 0, ',', '.') }}</span> @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-uppercase ls-1 text-danger"><i class="bi bi-arrow-up-circle me-2"></i>Pengeluaran</h6>
                </div>
                <div class="card-body p-0">
                    <div style="height: 400px; overflow-y: auto;" class="position-relative">
                        @forelse($pengeluaran as $tx)
                            <div class="list-group list-group-flush">
                                <div class="list-group-item p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 small">{{ $tx->description }}</h6>
                                        <span class="text-danger fw-bold small">-{{ number_format($tx->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($tx->date)->format('d M Y') }}</small>
                                        @if($tx->proof_image)
                                            <a href="{{ asset('storage/' . $tx->proof_image) }}" target="_blank" class="badge bg-light text-secondary border text-decoration-none"><i class="bi bi-image"></i> Bukti</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <i class="bi bi-cart-x display-4 text-muted opacity-25 mb-2"></i>
                                <p class="text-muted small mb-0">Belum ada pengeluaran.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="row mb-5">
        
        <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-pie-chart me-2"></i>Persentase Ketaatan
                </div>
                <div class="card-body">
                    <div style="height: 220px; width: 100%;">
                        <canvas id="statusSiswaChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-bar-chart me-2"></i>Arus Kas Bulan Ini
                </div>
                <div class="card-body">
                    <div style="height: 240px; width: 100%;">
                        <canvas id="akumulasiKasChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-8 mt-2 mt-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-bottom-0 small text-uppercase text-muted">
                    <i class="bi bi-graph-up-arrow me-2"></i>Tren Kas (30 Hari Terakhir)
                </div>
                <div class="card-body d-flex align-items-center">
                    <div style="height: 520px; width: 100%;">
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
    // --- DATA DARI CONTROLLER ---
    const siswaLunas = {{ $siswaLunas }};
    const siswaNunggak = {{ $siswaNunggak }};
    const totalMasuk = {{ $totalMasukBulanIni }};
    const totalKeluar = {{ $totalKeluarBulanIni }};
    const datesLabel = @json($dates);
    const dataPemasukan = @json($pemasukanPerHari);
    const dataPengeluaran = @json($pengeluaranPerHari);

    // 1. GRAFIK DONAT
    new Chart(document.getElementById('statusSiswaChart'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Nunggak'],
            datasets: [{ 
                data: [siswaLunas, siswaNunggak], 
                backgroundColor: ['#2AA5A5', '#DC3545'], 
                borderWidth: 0 
            }]
        },
        options: { 
            maintainAspectRatio: false, 
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
            layout: { padding: 10 }
        }
    });

    // 2. GRAFIK BATANG
    new Chart(document.getElementById('akumulasiKasChart'), {
        type: 'bar',
        data: {
            labels: ['Masuk', 'Keluar'],
            datasets: [{ 
                label: 'Rupiah', 
                data: [totalMasuk, totalKeluar], 
                backgroundColor: ['#2AA5A5', '#DC3545'], 
                borderRadius: 4,
                barThickness: 40
            }]
        },
        options: { 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } }, 
            scales: { 
                y: { beginAtZero: true, display: true, grid: { display: true } }, 
                x: { grid: { display: false } } 
            } 
        }
    });

    // 3. GRAFIK AREA (GUNUNG) - BESAR DI KANAN
    new Chart(document.getElementById('progresAreaChart'), {
        type: 'line',
        data: {
            labels: datesLabel,
            datasets: [
                { label: 'Pemasukan', data: dataPemasukan, borderColor: '#2AA5A5', backgroundColor: 'rgba(42, 165, 165, 0.1)', fill: true, tension: 0.4, pointRadius: 2, pointHoverRadius: 5 },
                { label: 'Pengeluaran', data: dataPengeluaran, borderColor: '#DC3545', backgroundColor: 'rgba(220, 53, 69, 0.1)', fill: true, tension: 0.4, pointRadius: 2, pointHoverRadius: 5 }
            ]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false, 
            interaction: { mode: 'index', intersect: false },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, 
                x: { grid: { display: false }, ticks: { 
                    ticks: { 
                            display: true,   // Munculkan label tanggal
                            autoSkip: true,  // Otomatis loncat kalau terlalu rapat
                            maxTicksLimit: 10
                         } 
                }
                        }
                          // Sembunyikan tanggal biar bersih
            },
            plugins: { legend: { position: 'top', align: 'end' } }
        }
    });
</script>
@endpush