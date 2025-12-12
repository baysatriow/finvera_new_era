@extends('layouts.dashboard')

@section('page_title', 'Tambah Rekening Baru')

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
    /* Input Styling */
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
                <h5 class="fw-bold mb-1 text-dark">Input Rekening Pencairan</h5>
                <p class="text-muted small mb-0">Pastikan data sesuai buku tabungan untuk kelancaran pencairan.</p>
            </div>
            <div class="card-body p-4 pt-0">

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 mb-4 border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('bank.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Bank / E-Wallet</label>
                        <select name="bank_name" class="form-select py-3 rounded-3" required>
                            <option value="">Pilih Bank...</option>
                            <option value="BCA">BCA</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BSI">BSI (Syariah)</option>
                            <option value="Jago">Bank Jago</option>
                            <option value="SeaBank">SeaBank</option>
                            <option value="GoPay">GoPay</option>
                            <option value="OVO">OVO</option>
                            <option value="Dana">DANA</option>
                            <option value="ShopeePay">ShopeePay</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nomor Rekening</label>
                        <input type="number" name="account_number" class="form-control py-3 rounded-3" placeholder="Contoh: 1234567890" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nama Pemilik</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="account_holder_name" class="form-control py-3 rounded-end-3 fw-bold text-dark" value="{{ Auth::user()->name }}" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="form-text text-muted small mt-2">
                            <i class="fas fa-lock me-1"></i> Terkunci sesuai nama akun (KTP) demi keamanan.
                        </div>
                    </div>

                    @php
                        $hasAccount = \App\Models\BankAccount::where('user_id', Auth::id())->exists();
                    @endphp

                    <div class="mb-4">
                        <label class="primary-check-card d-flex align-items-start gap-3 {{ !$hasAccount ? 'active' : '' }}" for="primaryCheck">
                            <div class="mt-1">
                                <input class="form-check-input fs-5" type="checkbox" name="is_primary" id="primaryCheck"
                                    {{ !$hasAccount ? 'checked onclick="return false;"' : '' }}>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Jadikan Rekening Utama</h6>
                                @if(!$hasAccount)
                                    <small class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Otomatis aktif untuk rekening pertama.</small>
                                @else
                                    <small class="text-muted">Rekening utama digunakan prioritas untuk pencairan dana.</small>
                                @endif
                            </div>
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-finvera py-3 rounded-3 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Rekening
                        </button>
                        <a href="{{ route('bank.index') }}" class="btn btn-light py-3 rounded-3 fw-bold text-muted">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
