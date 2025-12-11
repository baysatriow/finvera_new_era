@extends('layouts.dashboard')

@section('page_title', 'Tambah Rekening Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Input Data Rekening</h5>

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('bank.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Bank</label>
                        <select name="bank_name" class="form-select" required>
                            <option value="">Pilih Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BSI">BSI (Syariah)</option>
                            <option value="Jago">Bank Jago</option>
                            <option value="Gopay">GoPay</option>
                            <option value="Ovo">OVO</option>
                            <option value="Dana">DANA</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nomor Rekening / E-Wallet</label>
                        <input type="number" name="account_number" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder_name" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        <div class="form-text text-warning small">
                            <i class="fas fa-lock me-1"></i> Terkunci sesuai nama akun Anda ({{ Auth::user()->name }}).
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('bank.index') }}" class="btn btn-light w-50">Batal</a>
                        <button type="submit" class="btn btn-primary w-50">Simpan Rekening</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
