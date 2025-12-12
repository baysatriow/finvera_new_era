@extends('layouts.dashboard')

@section('page_title', 'Edit Rekening')

@section('content')
<style>
    .btn-finvera {
        background-color: #3A6D48 !important;
        color: white !important;
        border: none;
        transition: all 0.3s;
    }
    .btn-finvera:hover {
        background-color: #2c5236 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(58, 109, 72, 0.3);
    }

    .primary-check-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.3s;
        cursor: pointer;
        background-color: #fff;
    }
    .primary-check-card:hover {
        border-color: #3A6D48;
        background-color: #f9fdfa;
    }
    .primary-check-card.active {
        border-color: #3A6D48;
        background-color: #e8f5e9;
    }
    .form-check-input:checked {
        background-color: #3A6D48;
        border-color: #3A6D48;
    }
    .form-control, .form-select {
        border: 1px solid #eee;
        background-color: #f8f9fa;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #3A6D48;
        box-shadow: 0 0 0 0.2rem rgba(58, 109, 72, 0.1);
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-4 px-4 border-bottom-0">
                <h5 class="fw-bold mb-1 text-dark">Perbarui Data Rekening</h5>
                <p class="text-muted small mb-0">Ubah informasi rekening pencairan Anda.</p>
            </div>
            <div class="card-body p-4 pt-0">

                <form action="{{ route('bank.update', $account->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Bank / E-Wallet</label>
                        <select name="bank_name" class="form-select py-3 rounded-3" required>
                            <option value="">Pilih Bank...</option>
                            @foreach(['BCA','BRI','BNI','Mandiri','BSI','Jago','SeaBank','GoPay','OVO','Dana','ShopeePay'] as $bank)
                                <option value="{{ $bank }}" {{ $account->bank_name == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nomor Rekening</label>
                        <input type="number" name="account_number" class="form-control py-3 rounded-3" value="{{ $account->account_number }}" placeholder="Contoh: 1234567890" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Pemilik</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="account_holder_name" class="form-control py-3 rounded-end-3 fw-bold text-dark" value="{{ $account->account_holder_name }}" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="form-text text-muted small mt-2">
                            <i class="fas fa-lock me-1"></i> Nama tidak dapat diubah demi keamanan.
                        </div>
                    </div>

                    <!-- Option Jadikan Utama -->
                    <div class="mb-4">
                        @if($account->is_primary)
                            <div class="alert alert-success d-flex align-items-center py-3 px-3 border-0 bg-success bg-opacity-10 text-success rounded-3">
                                <i class="fas fa-check-circle fs-4 me-3"></i>
                                <div>
                                    <strong>Rekening Utama</strong>
                                    <div class="small">Rekening ini digunakan sebagai tujuan transfer otomatis.</div>
                                </div>
                            </div>
                        @else
                            <label class="primary-check-card d-flex align-items-start gap-3" for="primaryCheck">
                                <div class="mt-1">
                                    <input class="form-check-input fs-5" type="checkbox" name="is_primary" id="primaryCheck">
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Jadikan Rekening Utama</h6>
                                    <small class="text-muted">Aktifkan untuk menjadikan ini rekening prioritas pencairan.</small>
                                </div>
                            </label>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-finvera py-3 rounded-3 fw-bold shadow-lg">
                            <i class="fas fa-check me-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('bank.index') }}" class="btn btn-light py-3 rounded-3 fw-bold text-muted">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
