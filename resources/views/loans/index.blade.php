@extends('layouts.dashboard')

@section('page_title', 'Riwayat Pengajuan')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 5px 15px;
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
    .page-link:hover { color: #2c5236; }

    .btn-finvera-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        transition: all 0.3s;
    }
    .btn-finvera-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.2);
    }
    .btn-outline-custom {
        border: 1px solid #dee2e6;
        color: #3A6D48;
        background-color: white;
        transition: all 0.3s;
    }
    .btn-outline-custom:hover {
        border-color: #3A6D48;
        background-color: #f4fcf6;
        color: #2c5236;
    }

    .btn-disabled-custom {
        background-color: #e9ecef;
        color: #adb5bd;
        border: 1px solid #dee2e6;
        cursor: not-allowed;
    }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Data Riwayat Pinjaman</h5>
            <p class="text-muted small mb-0">Semua riwayat pengajuan dan status pembayaran Anda.</p>
        </div>

        <a href="{{ route('loans.create') }}" class="btn btn-finvera-solid rounded-pill px-4 shadow-sm fw-bold">
            <i class="fas fa-plus me-2"></i> Ajukan Baru
        </a>
    </div>

    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="loansTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 border-0 rounded-start">Tanggal</th>
                        <th class="border-0">ID Pinjaman</th>
                        <th class="border-0">Tujuan</th>
                        <th class="border-0">Nominal</th>
                        <th class="border-0">Tenor</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="ps-4">
                            <span class="d-none">{{ $app->created_at->format('Ymd') }}</span>
                            <div class="fw-bold text-dark">{{ $app->created_at->format('d M Y') }}</div>
                            <div class="small text-muted">{{ $app->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">#{{ str_pad($app->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 180px;" title="{{ $app->purpose }}">
                                {{ $app->purpose }}
                            </span>
                        </td>
                        <td class="fw-bold text-finvera">Rp {{ number_format($app->amount, 0, ',', '.') }}</td>
                        <td>{{ $app->tenor }} Bulan</td>
                        <td>
                            @php
                                $statusConfig = match($app->status) {
                                    'approved' => ['bg' => 'success', 'icon' => 'check-circle', 'text' => 'Disetujui'],
                                    'pending' => ['bg' => 'warning', 'icon' => 'clock', 'text' => 'Review'],
                                    'rejected' => ['bg' => 'danger', 'icon' => 'times-circle', 'text' => 'Ditolak'],
                                    'paid' => ['bg' => 'primary', 'icon' => 'check-double', 'text' => 'Lunas'],
                                    default => ['bg' => 'secondary', 'icon' => 'question-circle', 'text' => ucfirst($app->status)]
                                };
                            @endphp
                            <span class="badge bg-{{ $statusConfig['bg'] }} bg-opacity-10 text-{{ $statusConfig['bg'] }} px-3 py-2 rounded-pill">
                                <i class="fas fa-{{ $statusConfig['icon'] }} me-1"></i> {{ $statusConfig['text'] }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('loans.show', $app->id) }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 fw-bold">
                                Detail <i class="fas fa-arrow-right ms-1"></i>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#loansTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "&raquo;",
                    previous: "&laquo;"
                },
                zeroRecords: "Belum ada riwayat pinjaman."
            },
            order: [[ 0, "desc" ]],
            columnDefs: [
                { orderable: false, targets: 6 }
            ]
        });
    });
</script>
@endpush
@endsection
