@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-finvera">Buat Akun Baru</h3>
    <p class="text-muted">Langkah awal menuju kebebasan finansial syariah.</p>

    <div class="progress" style="height: 6px;">
        <div class="progress-bar bg-finvera" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="d-flex justify-content-between mt-2 small fw-bold">
        <span class="text-finvera">Tahap 1: Akun</span>
        <span class="text-muted opacity-50">Tahap 2: Data Diri</span>
    </div>
</div>

<form action="{{ route('register') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" placeholder="Sesuai KTP" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Username</label>
            <input type="text" name="username" class="form-control" placeholder="4-16 Karakter" minlength="4" maxlength="16" value="{{ old('username') }}" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Email</label>
        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Nomor WhatsApp</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted fw-bold">+62</span>

            <input type="number" name="phone" class="form-control" placeholder="81234567890" value="{{ old('phone') }}" required>
        </div>
        <div class="form-text text-muted small"><i class="fas fa-info-circle me-1"></i> Masukkan nomor HP aktif (contoh: 812xxxx atau 0812xxxx).</div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Kata Sandi</label>
            <div class="input-group">
                <input type="password" name="password" class="form-control border-end-0" id="password" placeholder="Min. 8 karakter" required>
                <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" onclick="togglePassword('password', 'iconPass')">
                    <i class="fas fa-eye text-muted" id="iconPass"></i>
                </span>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Konfirmasi Sandi</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" class="form-control border-end-0" id="password_confirm" placeholder="Ulangi sandi" required>
                <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" onclick="togglePassword('password_confirm', 'iconConfirm')">
                    <i class="fas fa-eye text-muted" id="iconConfirm"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-finvera btn-lg shadow-sm">
            Lanjut ke Tahap 2 <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>

    <div class="text-center mt-3">
        <p class="mb-0 text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-finvera fw-bold text-decoration-none">Masuk</a></p>
    </div>
</form>

@push('scripts')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
