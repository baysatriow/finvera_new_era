@extends('layouts.dashboard')

@section('page_title', 'Detail Konfirmasi')

@section('content')
<style>
    .proof-img {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
        background-color: #333;
        border-radius: 12px;
    }
    .btn-back-custom {
        background-color: white;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-back-custom:hover {
        background-color: #f8f9fa;
        color: #3A6D48;
        border-color: #3A6D48;
        transform: translateX(-3px);
    }
</style>

<div class="row g-4">
    <!-- Header Back Button (Fixed Visibility) -->
    <div class="col-12">
        <a href="{{ route('admin.payments.index') }}" class="btn-back-custom">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Antrean
        </a>
    </div>

    <!-- Kolom Kiri: Info Transaksi -->
    <div class="col-lg-5">

        <!-- Info Pembayaran -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-success text-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center rounded-top-4">
                <h6 class="fw-bold mb-0">Rincian Transaksi</h6>
                <span class="badge bg-white text-success">Waiting</span>
            </div>
            <div class="card-body p-4">
                <div class="mb-4 text-center">
                    <small class="text-muted text-uppercase fw-bold">Total Dibayar</small>
                    <h2 class="fw-bold text-dark mt-1">Rp {{ number_format($payment->amount + $payment->tazir_amount, 0, ',', '.') }}</h2>
                    <span class="badge bg-light text-dark border">
                        Cicilan Ke-{{ $payment->installment_number }}
                    </span>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between bg-white">
                        <span class="text-muted">Jatuh Tempo (Deadline)</span>
                        <span class="fw-bold text-danger">{{ $payment->due_date->format('d M Y') }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between bg-white">
                        <span class="text-muted">Tanggal Transfer User</span>
                        <span class="fw-bold text-dark">{{ $payment->paid_at->format('d M Y') }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between bg-white">
                        <span class="text-muted">Pokok</span>
                        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between bg-white">
                        <span class="text-muted">Denda (Ta'zir)</span>
                        <span class="text-danger">Rp {{ number_format($payment->tazir_amount, 0, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Info Peminjam -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Data Peminjam</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        {{ substr($payment->loan->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $payment->loan->user->name }}</h6>
                        <small class="text-muted">Kode Pinjaman: {{ $payment->loan->loan_code }}</small>
                    </div>
                </div>
                <div class="d-grid">
                    <a href="{{ route('admin.borrowers.show', $payment->loan->user_id) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill">
                        Lihat Profil User <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Bukti & Aksi -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Bukti Pembayaran</h6>
            </div>
            <div class="card-body p-4 pt-0">

                @if($payment->proof_path)
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/' . $payment->proof_path) }}" class="proof-img shadow-sm mb-2" alt="Bukti Transfer">
                        <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="btn btn-link text-decoration-none btn-sm">
                            <i class="fas fa-search-plus me-1"></i> Perbesar Gambar
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning">Tidak ada gambar bukti yang diunggah.</div>
                @endif

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-danger w-100 py-3 fw-bold rounded-3 btn-reject-page">
                            <i class="fas fa-times-circle me-2"></i> TOLAK PEMBAYARAN
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success w-100 py-3 fw-bold rounded-3 btn-approve-page shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> TERIMA & LUNAS
                        </button>
                    </div>
                </div>

                <!-- Forms -->
                <form id="approve-form-page" action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="d-none">@csrf</form>
                <form id="reject-form-page" action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="reason" id="reject-reason-page">
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Approve
        $('.btn-approve-page').click(function() {
            Swal.fire({
                title: 'Konfirmasi Terima',
                text: "Status cicilan akan berubah menjadi Lunas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Terima'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('approve-form-page').submit();
                }
            });
        });

        // Reject
        $('.btn-reject-page').click(function() {
            Swal.fire({
                title: 'Tolak Bukti',
                text: "Alasan penolakan:",
                input: 'text',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Tolak',
                inputValidator: (value) => {
                    if (!value) return 'Wajib isi alasan!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason-page').value = result.value;
                    Swal.showLoading();
                    document.getElementById('reject-form-page').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
