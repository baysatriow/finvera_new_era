@extends('layouts.dashboard')

@section('page_title', 'Admin Dashboard')

@section('content')
<style>
    /* Custom Button Styles */
    .btn-action-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-action-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.2);
    }

    .link-action {
        color: #3A6D48;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s;
    }
    .link-action:hover {
        color: #2c5236;
        text-decoration: underline;
    }
</style>

<div class="row g-4 mb-4">
    <!-- Statistik Cards -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Total User</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_users']) }}</h3>
                    </div>
                </div>
                <div class="small text-muted">
                    <span class="text-success fw-bold"><i class="fas fa-arrow-up"></i> +5%</span> dari bulan lalu
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="fas fa-file-contract fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Pinjaman Aktif</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_active_loans']) }}</h3>
                    </div>
                </div>
                <div class="small text-muted">Transaksi berjalan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Disbursed</h6>
                        <!-- Format dalam Juta -->
                        <h4 class="fw-bold mb-0 text-dark text-truncate" title="Rp {{ number_format($stats['total_disbursed']) }}">
                            Rp {{ number_format($stats['total_disbursed'] / 1000000, 1) }} Juta
                        </h4>
                    </div>
                </div>
                <div class="small text-muted">Dana tersalurkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white position-relative">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Pending Review</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['pending_applications']) }}</h3>
                    </div>
                </div>
                <!-- Tombol Proses Sekarang  -->
                <a href="{{ route('admin.applications') }}" class="btn btn-action-solid btn-sm w-100 mt-2 stretched-link">
                    Proses Sekarang <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- GRAFIK ANALITIK -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Analitik Penyaluran Dana</h6>

                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $filter == 'weekly' ? 'Mingguan' : ($filter == 'yearly' ? 'Tahunan' : 'Bulanan') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item {{ $filter == 'weekly' ? 'active' : '' }}" href="?filter=weekly">Mingguan (7 Hari)</a></li>
                        <li><a class="dropdown-item {{ $filter == 'monthly' ? 'active' : '' }}" href="?filter=monthly">Bulanan (Tahun Ini)</a></li>
                        <li><a class="dropdown-item {{ $filter == 'yearly' ? 'active' : '' }}" href="?filter=yearly">Tahunan (5 Tahun)</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="disbursementChart" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- PENGAJUAN TERBARU (SIDEBAR KANAN) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Pengajuan Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentApplications as $app)
                    <li class="list-group-item border-0 px-4 py-3 d-flex align-items-center hover-bg-light transition">
                        <div class="bg-light rounded-circle p-2 me-3 text-center d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <span class="fw-bold text-muted">{{ substr($app->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 text-dark small fw-bold text-truncate">{{ $app->user->name }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Rp {{ number_format($app->amount / 1000000, 1) }} Juta &bull; {{ $app->tenor }} Bln
                            </small>
                        </div>
                        <div class="ms-2">
                            @if($app->status == 'pending')
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.6rem;">Pending</span>
                            @elseif($app->status == 'approved')
                                <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.6rem;">Approved</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.6rem;">Rejected</span>
                            @endif
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item border-0 px-4 py-5 text-center text-muted">
                        Belum ada pengajuan baru.
                    </li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white border-0 text-center pb-4 pt-0">
                <!-- Tombol Lihat Semua (Fixed Style) -->
                <a href="{{ route('admin.applications') }}" class="link-action small">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('disbursementChart').getContext('2d');

        const labels = @json($chartData['labels']);
        const values = @json($chartData['values']);

        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(58, 109, 72, 0.2)');
        gradient.addColorStop(1, 'rgba(58, 109, 72, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penyaluran (Rp)',
                    data: values,
                    borderColor: '#3A6D48',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3A6D48',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    // Format tooltip dalam Juta
                                    let valInMillion = (context.parsed.y / 1000000).toFixed(1);
                                    label += 'Rp ' + valInMillion + ' Juta';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) {
                                // Format sumbu Y dalam Juta
                                return (value / 1000000) + ' Juta';
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    });
</script>
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
    .transition {
        transition: background-color 0.2s ease;
    }
</style>
@endsection
