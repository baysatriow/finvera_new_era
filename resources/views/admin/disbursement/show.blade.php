@extends('layouts.dashboard')

@section('page_title', 'Detail Pembayaran')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Card Styles */
    .detail-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: white;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header-custom {
        background-color: #fff;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Foto Aset Center & Contain */
    .doc-img-container {
        height: 180px;
        width: 100%;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .doc-img {
        max-height: 95%;
        max-width: 95%;
        object-fit: contain;
        transition: transform 0.3s;
    }
    .doc-img-container:hover .doc-img { transform: scale(1.05); }

    /* Tombol Overlay */
    .doc-overlay-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.6);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .doc-img-container:hover .doc-overlay-btn { opacity: 1; }

    /* Button Styles */
    .btn-outline-custom {
        border: 1px solid #3A6D48;
        color: #3A6D48;
        background-color: white;
        font-weight: 600;
    }
    .btn-outline-custom:hover {
        background-color: #3A6D48;
        color: white;
    }

    .btn-back-custom {
        background-color: white;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-back-custom:hover {
        background-color: #f8f9fa;
        color: #3A6D48;
        border-color: #3A6D48;
        transform: translateX(-3px);
    }
</style>
@endpush

<div class="row g-4">
    <!-- Header Back Button -->
    <div class="col-12">
        <a href="{{ route('admin.disbursement.index') }}" class="btn-back-custom">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Monitoring
        </a>
    </div>

    <!-- STATISTIK RINGKAS (HEADER) -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                    <div class="card-body p-4">
                        <small class="text-white-50 text-uppercase fw-bold">Total Pinjaman</small>
                        <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</h3>
                        <div class="mt-2 small opacity-75 font-monospace">Kode: {{ $loan->loan_code }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <small class="text-muted text-uppercase fw-bold">Sudah Dibayar</small>
                        <h3 class="fw-bold mb-0 mt-1 text-success">Rp {{ number_format($paidAmount, 0, ',', '.') }}</h3>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <small class="text-muted text-uppercase fw-bold">Sisa Tagihan</small>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</h3>
                        <div class="mt-2 small text-muted">
                            Jatuh Tempo: {{ $loan->due_date->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO PEMINJAM & ASET -->
    <div class="col-md-4">
        <div class="card detail-card">
            <div class="card-header-custom">
                <h6 class="fw-bold mb-0 text-dark">Informasi Peminjam</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light rounded-circle p-3 me-3 text-finvera fw-bold fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{ substr($loan->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $loan->user->name }}</h5>
                        <div class="small text-muted">{{ $loan->user->email }}</div>
                    </div>
                </div>

                <ul class="list-group list-group-flush small mb-3">
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Nomor HP</span>
                        <span class="fw-bold text-dark">{{ $loan->user->phone }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Pekerjaan</span>
                        <span class="fw-bold text-dark">{{ $loan->user->job ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0">
                        <span class="text-muted d-block mb-1">Alamat</span>
                        <span class="fw-bold d-block lh-sm text-dark">{{ $loan->user->address_full ?? '-' }}</span>
                    </li>
                </ul>

                <div class="d-grid">
                    <a href="{{ route('admin.borrowers.show', $loan->user_id) }}" target="_blank" class="btn btn-outline-custom btn-sm rounded-pill fw-bold py-2">
                        Lihat Profil Lengkap <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Asset Preview -->
        <div class="card detail-card">
            <div class="card-header-custom">
                <h6 class="fw-bold mb-0 text-dark">Aset Jaminan</h6>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Jenis Aset</small>
                        <strong class="text-dark">{{ $loan->application->asset_type ?? 'Aset' }}</strong>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Nilai</small>
                        <strong class="text-dark">Rp {{ number_format($loan->application->asset_value ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                         <label class="small text-muted mb-1 fw-bold">Dokumen</label>
                         @if($loan->application && $loan->application->asset_document_path)
                            <div class="doc-img-container">
                                <img src="{{ asset('storage/' . $loan->application->asset_document_path) }}" class="doc-img" alt="Dokumen">
                                <a href="{{ asset('storage/' . $loan->application->asset_document_path) }}" target="_blank" class="doc-overlay-btn"><i class="fas fa-expand-alt"></i></a>
                            </div>
                        @else
                            <div class="doc-img-container text-muted small">No Image</div>
                        @endif
                    </div>
                     <div class="col-6">
                         <label class="small text-muted mb-1 fw-bold">Selfie</label>
                         @if($loan->application && $loan->application->asset_selfie_path)
                            <div class="doc-img-container">
                                <img src="{{ asset('storage/' . $loan->application->asset_selfie_path) }}" class="doc-img" alt="Selfie">
                                <a href="{{ asset('storage/' . $loan->application->asset_selfie_path) }}" target="_blank" class="doc-overlay-btn"><i class="fas fa-expand-alt"></i></a>
                            </div>
                        @else
                            <div class="doc-img-container text-muted small">No Image</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL CICILAN -->
    <div class="col-md-8">
        <div class="card detail-card h-100">
            <div class="card-header-custom">
                <h5 class="fw-bold mb-0 text-dark">Jadwal Cicilan</h5>
                @if($loan->status == 'paid')
                    <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-double me-1"></i> LUNAS TOTAL</span>
                @else
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Aktif</span>
                @endif
            </div>
            <div class="card-body px-4 pt-0 pb-4">
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle w-100" id="disbursementInstallmentTable">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Ke-</th>
                                <th>Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th>Denda</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loan->installments as $ins)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $ins->installment_number }}</td>
                                <td>
                                    <span class="{{ $ins->status != 'paid' && $ins->due_date < now() ? 'text-danger fw-bold' : '' }}">
                                        {{ $ins->due_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($ins->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($ins->tazir_amount > 0)
                                        <span class="text-danger small">+{{ number_format($ins->tazir_amount, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">
                                    Rp {{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($ins->status == 'waiting')
                                        <span class="badge bg-info text-white rounded-pill px-3">Verifikasi</span>
                                    @elseif($ins->status == 'failed')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Ditolak</span>
                                    @elseif($ins->status == 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                                    @elseif($ins->status == 'late')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Terlambat</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ ucfirst($ins->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($ins->status == 'waiting')
                                        <!-- Tombol Lihat Bukti & Verifikasi -->
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $ins->id }}">
                                            <i class="fas fa-search me-1"></i> Cek
                                        </button>

                                        <!-- Modal Verifikasi (FIXED: Hapus tabindex agar SWAL input bisa fokus) -->
                                        <div class="modal fade" id="verifyModal{{ $ins->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4">
                                                    <div class="modal-header border-bottom-0 bg-primary text-white rounded-top-4">
                                                        <h6 class="fw-bold mb-0">Verifikasi Pembayaran #{{ $ins->installment_number }}</h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-4">
                                                        @if($ins->proof_path)
                                                            <div class="border rounded bg-light p-2 mb-3">
                                                                <img src="{{ asset('storage/' . $ins->proof_path) }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                                                            </div>
                                                            <a href="{{ asset('storage/' . $ins->proof_path) }}" target="_blank" class="btn btn-sm btn-light border mb-2">
                                                                <i class="fas fa-expand me-1"></i> Lihat Ukuran Asli
                                                            </a>
                                                        @else
                                                            <div class="alert alert-warning">Tidak ada bukti foto yang diunggah.</div>
                                                        @endif

                                                        <div class="mt-3 text-start bg-light p-3 rounded-3">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted small">Tanggal Bayar User:</span>
                                                                <strong>{{ $ins->paid_at ? $ins->paid_at->format('d M Y') : '-' }}</strong>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted small">Nominal:</span>
                                                                <strong class="text-success">Rp {{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-center border-top-0 pb-4 pt-0 px-4">
                                                        <form action="{{ route('admin.disbursement.verify', $ins->id) }}" method="POST" class="w-100 d-flex gap-2" id="verifyForm{{ $ins->id }}">
                                                            @csrf
                                                            <!-- Hidden Input Reason -->
                                                            <input type="hidden" name="reason" id="rejectReason{{ $ins->id }}">
                                                            <!-- Hidden Input Action -->
                                                            <input type="hidden" name="action" id="verifyAction{{ $ins->id }}">

                                                            <button type="button" class="btn btn-outline-danger rounded-pill fw-bold flex-fill py-2 btn-reject-payment" data-id="{{ $ins->id }}">
                                                                <i class="fas fa-times me-1"></i> Tolak
                                                            </button>
                                                            <button type="button" class="btn btn-success rounded-pill fw-bold flex-fill py-2 btn-approve-payment" data-id="{{ $ins->id }}">
                                                                <i class="fas fa-check me-1"></i> Terima & Lunas
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($ins->status == 'failed')
                                        <button class="btn btn-sm btn-link text-danger p-0" onclick="Swal.fire('Alasan Penolakan', '{{ $ins->rejection_reason }}', 'error')">Info</button>
                                    @elseif($ins->status == 'paid')
                                         <span class="text-success small fw-bold"><i class="fas fa-check-double"></i> Verified</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
        $('#disbursementInstallmentTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            searching: false,
            paging: true,
            pageLength: 6,
            lengthChange: false,
            info: false,
            ordering: false
        });

        // HANDLER APPROVE PAYMENT
        $('.btn-approve-payment').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const form = document.getElementById('verifyForm' + id);
            const actionInput = document.getElementById('verifyAction' + id);

            Swal.fire({
                title: 'Terima Pembayaran?',
                text: "Status cicilan akan diubah menjadi Lunas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima'
            }).then((result) => {
                if (result.isConfirmed) {
                    actionInput.value = 'approve';
                    Swal.fire({title: 'Memproses...', didOpen: () => Swal.showLoading()});
                    form.submit();
                }
            });
        });

        // HANDLER REJECT PAYMENT
        $('.btn-reject-payment').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const form = document.getElementById('verifyForm' + id);
            const reasonInput = document.getElementById('rejectReason' + id);
            const actionInput = document.getElementById('verifyAction' + id);

            // Hide the Bootstrap modal first to avoid focus conflict
            $('#verifyModal' + id).modal('hide');

            Swal.fire({
                title: 'Tolak Pembayaran',
                text: "Berikan alasan penolakan:",
                input: 'text',
                inputPlaceholder: 'Contoh: Foto buram, nominal tidak sesuai...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak',
                inputValidator: (value) => {
                    if (!value) return 'Wajib mengisi alasan penolakan!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    reasonInput.value = result.value;
                    actionInput.value = 'reject';
                    Swal.fire({title: 'Menolak...', didOpen: () => Swal.showLoading()});
                    form.submit();
                } else {
                    // Show modal again if cancelled
                    $('#verifyModal' + id).modal('show');
                }
            });
        });
    });
</script>
@endpush
@endsection
