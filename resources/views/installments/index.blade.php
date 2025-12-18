@extends('layouts.dashboard')

@section('page_title', 'Tagihan Saya')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Styling DataTables */
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
    .page-link:hover { color: #2c5236; }

    /* Button Styles */
    .btn-pay-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 0.85rem;
    }
    .btn-pay-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.3);
    }

    .btn-retry {
        background-color: #dc3545;
        color: white;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .btn-retry:hover {
        background-color: #bb2d3b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    /* Status Badge Fixed Width */
    .badge-status {
        min-width: 100px;
        padding: 8px 12px;
    }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0">
        <h5 class="fw-bold mb-0 text-dark">Tagihan Cicilan Aktif</h5>
        <p class="text-muted small mb-0">Daftar cicilan yang perlu segera dibayarkan.</p>
    </div>

    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="installmentsTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 border-0 rounded-start">Jatuh Tempo</th>
                        <th class="border-0">Kode Pinjaman</th>
                        <th class="border-0">Cicilan Ke</th>
                        <th class="border-0">Nominal Tagihan</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($installments as $ins)
                    <tr>
                        <td class="ps-4">
                            <span class="d-none">{{ $ins->due_date->format('Ymd') }}</span> <!-- Helper Sorting -->

                            @if($ins->status == 'late' || $ins->status == 'failed')
                                <div class="text-danger fw-bold">{{ $ins->due_date->format('d M Y') }}</div>
                                @if($ins->status == 'failed')
                                    <small class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Ditolak</small>
                                @else
                                    <small class="text-danger"><i class="fas fa-exclamation-circle"></i> Telat</small>
                                @endif
                            @else
                                <div class="text-dark fw-bold">{{ $ins->due_date->format('d M Y') }}</div>
                                @if($ins->due_date->isToday())
                                    <small class="text-warning fw-bold">Hari Ini!</small>
                                @else
                                    <small class="text-muted">{{ $ins->due_date->locale('id')->diffForHumans() }}</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $ins->loan->loan_code }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">Bulan {{ $ins->installment_number }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-finvera">Rp {{ number_format($ins->amount, 0, ',', '.') }}</div>
                            @if($ins->tazir_amount > 0)
                                <small class="text-danger fw-bold" style="font-size: 0.7rem;">+ Denda Rp {{ number_format($ins->tazir_amount, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusConfig = match($ins->status) {
                                    'failed' => ['bg' => 'danger', 'icon' => 'times-circle', 'text' => 'Gagal / Ditolak'],
                                    'late' => ['bg' => 'danger', 'icon' => 'exclamation-circle', 'text' => 'Terlambat'],
                                    'pending' => ['bg' => 'warning', 'icon' => 'clock', 'text' => 'Belum Bayar'],
                                    'waiting' => ['bg' => 'info', 'icon' => 'hourglass-half', 'text' => 'Verifikasi'],
                                    'paid' => ['bg' => 'success', 'icon' => 'check-circle', 'text' => 'Lunas'],
                                    default => ['bg' => 'light', 'icon' => 'question', 'text' => ucfirst($ins->status)]
                                };
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $statusConfig['bg'] }} bg-opacity-10 text-{{ $statusConfig['bg'] }} rounded-pill badge-status">
                                    <i class="fas fa-{{ $statusConfig['icon'] }} me-1"></i> {{ $statusConfig['text'] }}
                                </span>

                                <!-- Tombol Info Alasan Penolakan -->
                                @if($ins->status == 'failed' && $ins->rejection_reason)
                                    <button type="button" class="btn btn-sm btn-light text-danger border rounded-circle shadow-sm"
                                            onclick="showRejectionReason('{{ addslashes($ins->rejection_reason) }}')"
                                            title="Lihat Alasan">
                                        <i class="fas fa-info"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            @if($ins->status == 'waiting')
                                <button class="btn btn-light btn-sm px-3 rounded-pill border text-muted" disabled>
                                    <i class="fas fa-check me-1"></i> Dikirim
                                </button>
                            @elseif($ins->status == 'failed')
                                <a href="{{ route('installments.pay', $ins->id) }}" class="btn btn-retry btn-sm px-3 rounded-pill shadow-sm">
                                    <i class="fas fa-redo me-1"></i> Upload Ulang
                                </a>
                            @else
                                <a href="{{ route('installments.pay', $ins->id) }}" class="btn btn-pay-solid btn-sm px-4 rounded-pill shadow-sm">
                                    Bayar Sekarang
                                </a>
                            @endif
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
        $('#installmentsTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada tagihan aktif",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "&raquo;",
                    previous: "&laquo;"
                },
                zeroRecords: "Hore! Tidak ada tagihan cicilan aktif saat ini."
            },
            order: [[ 0, "asc" ]],
            columnDefs: [
                { orderable: false, targets: 5 }
            ]
        });
    });

    // Fungsi Popup Alasan Penolakan
    function showRejectionReason(reason) {
        Swal.fire({
            icon: 'error',
            title: 'Pembayaran Ditolak',
            html: `<div class="text-start bg-light p-3 rounded border text-danger small">${reason}</div>`,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Tutup',
            footer: '<span class="text-muted small">Silakan perbaiki dan upload ulang bukti pembayaran.</span>'
        });
    }
</script>
@endpush
@endsection
