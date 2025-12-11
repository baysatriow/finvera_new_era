@extends('layouts.dashboard')

@section('page_title', 'Admin Dashboard')

@section('content')
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
                <div class="small text-muted">Transkasi berjalan</div>
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
                        <h4 class="fw-bold mb-0 text-dark text-truncate" title="Rp {{ number_format($stats['total_disbursed']) }}">
                            Rp {{ number_format($stats['total_disbursed'] / 1000000, 1) }}Jt
                        </h4>
                    </div>
                </div>
                <div class="small text-muted">Dana tersalurkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 position-relative overflow-hidden">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Pending Review</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['pending_applications']) }}</h3>
                    </div>
                </div>
                <a href="{{ route('admin.applications') }}" class="stretched-link btn btn-sm btn-light w-100 mt-2">
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
                    <li class="list-group-item border-0 px-4 py-3 d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3 text-center" style="width: 40px; height: 40px;">
                            <span class="fw-bold text-muted">{{ substr($app->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-dark small fw-bold">{{ $app->user->name }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Rp {{ number_format($app->amount / 1000000, 1) }} Juta &bull; {{ $app->tenor }} Bln
                            </small>
                        </div>
                        <div>
                            @if($app->status == 'pending')
                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Pending</span>
                            @elseif($app->status == 'approved')
                                <span class="badge bg-success" style="font-size: 0.65rem;">Approved</span>
                            @else
                                <span class="badge bg-danger" style="font-size: 0.65rem;">Rejected</span>
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
            <div class="card-footer bg-white border-0 text-center pb-3">
                <a href="{{ route('admin.applications') }}" class="text-decoration-none small fw-bold text-finvera">
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

        // Data dari Controller
        const labels = @json($chartData['labels']);
        const values = @json($chartData['values']);

        // Gradient Background
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(58, 109, 72, 0.2)'); // Warna FinVera Transparan
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
                    tension: 0.4 // Garis melengkung halus
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
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
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
                                return 'Rp ' + (value / 1000000) + 'jt';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection
