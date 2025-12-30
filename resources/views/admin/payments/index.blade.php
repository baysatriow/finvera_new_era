@extends('layouts.dashboard')

@section('page_title', 'Konfirmasi Pembayaran')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Styling Tombol Aksi Solid & Bulat */
    .btn-action-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        border: none;
        color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .btn-action-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        color: white;
        filter: brightness(1.1);
    }

    /* Warna Spesifik */
    .btn-approve { background-color: #198754; }
    .btn-reject { background-color: #dc3545; }
    .btn-detail { background-color: #0d6efd; }

    /* Tombol Lihat Foto yang Jelas */
    .btn-view-proof {
        background-color: #e7f1ff;
        color: #0d6efd;
        border: 1px solid #b6d4fe;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 50px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-view-proof:hover {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }

    /* Layout Table */
    .dataTables_wrapper .dataTables_filter input { border-radius: 20px; padding: 6px 15px; border: 1px solid #dee2e6; }
    .page-item.active .page-link { background-color: #3A6D48; border-color: #3A6D48; color: white; }
    .page-link { color: #3A6D48; }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Antrean Pembayaran Masuk</h5>
            <p class="text-muted small mb-0">Verifikasi bukti transfer dari peminjam.</p>
        </div>
        @if($payments->count() > 0)
        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
            {{ $payments->count() }} Perlu Cek
        </span>
        @endif
    </div>

    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="paymentsTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 rounded-start">Tanggal & Peminjam</th>
                        <th>Tagihan</th>
                        <th>Nominal Bayar</th>
                        <th>Bukti Transfer</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $pay)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center mb-1">
                                <div class="bg-light rounded-circle p-2 me-2 text-center fw-bold text-finvera small" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($pay->loan->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $pay->loan->user->name }}</div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">{{ $pay->paid_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                Kode: {{ $pay->loan->loan_code }} (Bln {{ $pay->installment_number }})
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-success">Rp {{ number_format($pay->amount + $pay->tazir_amount, 0, ',', '.') }}</div>
                            @if($pay->tazir_amount > 0)
                                <div class="text-danger fw-bold" style="font-size: 0.65rem;">
                                    (Pokok: {{ number_format($pay->amount, 0, ',', '.') }} + Denda: {{ number_format($pay->tazir_amount, 0, ',', '.') }})
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($pay->proof_path)
                                <a href="{{ asset('storage/' . $pay->proof_path) }}" target="_blank" class="btn-view-proof">
                                    <i class="fas fa-image"></i> Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted small fst-italic">Tidak ada bukti</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <!-- Tombol Reject (Merah) -->
                                <button type="button" class="btn-action-icon btn-reject btn-reject-action"
                                        data-id="{{ $pay->id }}" title="Tolak Pembayaran">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Tombol Approve (Hijau) -->
                                <form action="{{ route('admin.payments.approve', $pay->id) }}" method="POST" id="approve-form-{{ $pay->id }}">
                                    @csrf
                                    <button type="button" class="btn-action-icon btn-approve btn-approve-action"
                                            data-id="{{ $pay->id }}" data-amount="{{ number_format($pay->amount, 0, ',', '.') }}" title="Terima Pembayaran">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <!-- Tombol Detail (Biru) -->
                                <a href="{{ route('admin.payments.show', $pay->id) }}" class="btn-action-icon btn-detail" title="Detail Lengkap">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form Reject Tersembunyi (Akan diisi JS) -->
<form id="reject-form-global" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="reason" id="reject-reason-global">
</form>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#paymentsTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[ 0, "asc" ]], // Urutkan tanggal bayar terlama (paling urgent)
            columnDefs: [{ orderable: false, targets: 4 }]
        });

        // Handler Approve
        $('.btn-approve-action').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const amount = $(this).data('amount');

            Swal.fire({
                title: 'Terima Pembayaran?',
                text: `Nominal Rp ${amount} akan dicatat sebagai lunas.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({title: 'Memproses...', didOpen: () => Swal.showLoading()});
                    document.getElementById(`approve-form-${id}`).submit();
                }
            });
        });

        // Handler Reject
        $('.btn-reject-action').click(function() {
            const id = $(this).data('id');
            const form = document.getElementById('reject-form-global');
            form.action = `/admin/payments/${id}/reject`;

            Swal.fire({
                title: 'Tolak Pembayaran',
                text: "Berikan alasan penolakan (misal: Bukti buram, nominal salah):",
                input: 'text',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak',
                inputValidator: (value) => {
                    if (!value) return 'Alasan wajib diisi!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason-global').value = result.value;
                    Swal.fire({title: 'Menolak...', didOpen: () => Swal.showLoading()});
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
