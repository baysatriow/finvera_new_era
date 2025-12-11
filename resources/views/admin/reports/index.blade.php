@extends('layouts.dashboard')

@section('page_title', 'Laporan & Analitik')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-finvera w-100 fw-bold">
                            <i class="fas fa-filter me-2"></i> Tampilkan
                        </button>
                        <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success w-100 fw-bold">
                            <i class="fas fa-file-excel me-2"></i> Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- KARTU STATISTIK -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-primary">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase fw-bold small mb-0">Total Pencairan (Out)</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</h3>
                <small class="text-muted">Dana disalurkan ke peminjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-success">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase fw-bold small mb-0">Total Pembayaran (In)</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded p-2">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalRepayment, 0, ',', '.') }}</h3>
                <small class="text-muted">Cicilan diterima dari peminjam</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-warning">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase fw-bold small mb-0">Pendapatan Admin</h6>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <small class="text-muted">Estimasi dari biaya admin awal</small>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK ARUS KAS -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Analitik Arus Kas (Cashflow)</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="cashflowChart" style="height: 350px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('cashflowChart').getContext('2d');

        const labels = @json($chartData['labels']);
        const disbursementData = @json($chartData['disbursement']);
        const repaymentData = @json($chartData['repayment']);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pencairan (Keluar)',
                        data: disbursementData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pembayaran (Masuk)',
                        data: repaymentData,
                        backgroundColor: 'rgba(58, 109, 72, 0.7)',
                        borderColor: '#3A6D48',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
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
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
