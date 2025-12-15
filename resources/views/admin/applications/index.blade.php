@extends('layouts.dashboard')

@section('page_title', 'Persetujuan Pinjaman')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .btn-action-view {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
        padding: 6px 18px;
    }
    .btn-action-view:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.25);
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 6px 15px;
        border: 1px solid #dee2e6;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 5px 30px 5px 15px;
        border: 1px solid #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #3A6D48;
        border-color: #3A6D48;
        color: white;
    }
    .page-link { color: #3A6D48; }

    .badge-score {
        min-width: 60px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Antrean Pengajuan Masuk</h5>
            <p class="text-muted small mb-0">Tinjau profil risiko dan setujui pencairan dana.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm border border-warning">
                <i class="fas fa-clock me-1"></i> Menunggu: {{ $applications->count() }}
            </span>
        </div>
    </div>

    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="applicationsTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 rounded-start">Peminjam</th>
                        <th>Nominal & Tenor</th>
                        <th>Gaji Bulanan</th>
                        <th class="text-center">AI Score</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($app->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $app->user->name }}</div>
                                    <div class="small text-muted">{{ $app->user->job ?? 'Tidak ada data' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">Rp {{ number_format($app->amount, 0, ',', '.') }}</div>
                            <div class="small text-muted">{{ $app->tenor }} Bulan</div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">Rp {{ number_format($app->user->monthly_income, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $scoreColor = $app->ai_score >= 75 ? 'success' : ($app->ai_score >= 50 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $scoreColor }} bg-opacity-10 text-{{ $scoreColor }} rounded-pill px-3 badge-score border border-{{ $scoreColor }}">
                                {{ $app->ai_score }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark">{{ $app->created_at->format('d M Y') }}</div>
                            <div class="small text-muted">{{ $app->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-sm btn-action-view rounded-pill px-4 shadow-sm">
                                Review <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#applicationsTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[ 3, "desc" ]],
            columnDefs: [
                { orderable: false, targets: 5 }
            ]
        });
    });
</script>
@endpush
@endsection
