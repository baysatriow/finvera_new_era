@extends('layouts.dashboard')

@section('page_title', 'Laporan & Analitik')

@section('content')
<style>
    /* Custom Date Input & Buttons */
    .form-control-date {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
        height: 48px;
    }
    .btn-filter-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        height: 48px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-filter-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(58, 109, 72, 0.2);
    }
    .btn-excel-solid {
        background-color: #198754;
        color: white;
        border: none;
        height: 48px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-excel-solid:hover {
        background-color: #146c43;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }
    .stat-card-modern {
        border: none;
        border-radius: 16px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        height: 100%;
        transition: transform 0.2s;
    }
    .stat-card-modern:hover { transform: translateY(-2px); }
</style>

<!-- FITUR: FILTER PERIODE -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-filter me-2 text-finvera"></i>Filter Periode Laporan</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <form action="{{ route('admin.reports.index') }}" method="GET" class="row align-items-end g-3">
                    <!-- Sub: Input Tanggal -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-date" value="{{ $endDate }}">
                    </div>

                    <!-- Sub: Tombol Aksi -->
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-filter-solid w-100 rounded-3">
                                <i class="fas fa-search me-2"></i> Tampilkan
                            </button>
                            <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-excel-solid w-100 rounded-3">
                                <i class="fas fa-file-excel me-2"></i> Unduh CSV
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FITUR: KARTU STATISTIK -->
<div class="row g-4 mb-4">

    <!-- Sub: Total Pencairan -->
    <div class="col-md-4">
        <div class="stat-card-modern p-4 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Pencairan</h6>
                <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</h3>
                <small class="text-muted">Dana Keluar (Out)</small>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-paper-plane fa-lg"></i>
            </div>
        </div>
    </div>

    <!-- Sub: Total Pembayaran -->
    <div class="col-md-4">
        <div class="stat-card-modern p-4 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Pembayaran</h6>
                <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalRepayment, 0, ',', '.') }}</h3>
                <small class="text-muted">Dana Masuk (In)</small>
            </div>
            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-wallet fa-lg"></i>
            </div>
        </div>
    </div>

    <!-- Sub: Pendapatan Admin -->
    <div class="col-md-4">
        <div class="stat-card-modern p-4 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase fw-bold small mb-1">Pendapatan Admin</h6>
                <h3 class="fw-bold mb-0 text-warning">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <small class="text-muted">Profit Estimasi</small>
            </div>
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-coins fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- FITUR: GRAFIK ARUS KAS -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Grafik Arus Kas Harian</h6>
                <span class="badge bg-light text-dark border">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div style="height: 350px;">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LOGIC: CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('cashflowChart').getContext('2d');

        // Data passing dari Controller
        const labels = @json($chartData['labels']);
        const disbursementData = @json($chartData['disbursement']);
        const repaymentData = @json($chartData['repayment']);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pencairan (Out)',
                        data: disbursementData,
                        backgroundColor: '#3498db',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Pembayaran (In)',
                        data: repaymentData,
                        backgroundColor: '#3A6D48',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000) return 'Rp ' + (value/1000000) + 'jt';
                                return 'Rp ' + value;
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
