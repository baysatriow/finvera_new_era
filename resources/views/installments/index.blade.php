@extends('layouts.dashboard')

@section('page_title', 'Cicilan Saya')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Styling DataTables */
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

    /* Button Styles */
    .btn-pay-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-pay-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.3);
    }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0">
        <h5 class="fw-bold mb-0 text-dark">Jadwal Pembayaran Cicilan</h5>
        <p class="text-muted small mb-0">Pantau jatuh tempo dan lakukan pembayaran tepat waktu.</p>
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
                            <span class="d-none">{{ $ins->due_date->format('Ymd') }}</span>

                            @if($ins->status == 'late')
                                <div class="text-danger fw-bold">{{ $ins->due_date->format('d M Y') }}</div>
                                <small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Telat</small>
                            @elseif($ins->status == 'paid')
                                <div class="text-success fw-bold">{{ $ins->due_date->format('d M Y') }}</div>
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
                                <small class="text-danger">+ Denda Rp {{ number_format($ins->tazir_amount, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusConfig = match($ins->status) {
                                    'paid' => ['bg' => 'success', 'icon' => 'check-circle', 'text' => 'Lunas'],
                                    'late' => ['bg' => 'danger', 'icon' => 'exclamation-circle', 'text' => 'Terlambat'],
                                    'pending' => ['bg' => 'secondary', 'icon' => 'clock', 'text' => 'Belum Bayar'],
                                    default => ['bg' => 'light', 'icon' => 'question', 'text' => ucfirst($ins->status)]
                                };
                                if($ins->status == 'pending') {
                                    $statusConfig['bg'] = 'warning';
                                    $statusConfig['text'] = 'Tagihan Aktif';
                                }
                            @endphp
                            <span class="badge bg-{{ $statusConfig['bg'] }} bg-opacity-10 text-{{ $statusConfig['bg'] }} px-3 py-2 rounded-pill">
                                <i class="fas fa-{{ $statusConfig['icon'] }} me-1"></i> {{ $statusConfig['text'] }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            @if($ins->status != 'paid')
                                <form action="{{ route('installments.pay', $ins->id) }}" method="POST" id="pay-form-{{ $ins->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-pay-solid btn-sm px-4 rounded-pill shadow-sm btn-pay-confirm"
                                            data-form-id="pay-form-{{ $ins->id }}"
                                            data-month="{{ $ins->installment_number }}"
                                            data-amount="{{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}">
                                        Bayar Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="text-success small fw-bold">
                                    <i class="fas fa-check-double me-1"></i> Lunas pada {{ $ins->paid_at ? $ins->paid_at->format('d/m/y') : '-' }}
                                </div>
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
        // Init DataTables
        $('#installmentsTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada tagihan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "&raquo;",
                    previous: "&laquo;"
                },
                zeroRecords: "Belum ada riwayat cicilan."
            },
            order: [[ 0, "asc" ]],
            columnDefs: [
                { orderable: false, targets: 5 }
            ]
        });

        $('#installmentsTable').on('click', '.btn-pay-confirm', function() {
            const formId = $(this).data('form-id');
            const month = $(this).data('month');
            const amount = $(this).data('amount');

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="mb-3">Anda akan membayar cicilan ke-<strong>${month}</strong></div>
                    <h2 class="text-success fw-bold">Rp ${amount}</h2>
                    <small class="text-muted">Pastikan saldo Anda mencukupi untuk simulasi ini.</small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3A6D48',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Bayar Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById(formId).submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
