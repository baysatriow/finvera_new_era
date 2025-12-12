@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-finvera">Selamat Datang Kembali!</h3>
    <p class="text-muted">Masuk untuk melanjutkan akses pembiayaan syariah.</p>
</div>

<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="login" class="form-label fw-bold small text-uppercase text-muted">Username / Email</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" name="login" class="form-control border-start-0 ps-0" id="login" placeholder="email@contoh.com atau username" value="{{ old('login') }}" required autofocus>
        </div>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label fw-bold small text-uppercase text-muted">Kata Sandi</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
            <input type="password" name="password" class="form-control border-start-0 border-end-0 ps-0" id="password" placeholder="********" required>
            <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" onclick="togglePassword()">
                <i class="fas fa-eye text-muted" id="toggleIcon"></i>
            </span>
        </div>
    </div>

    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-finvera btn-lg shadow-sm">
            Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>

    <div class="text-center">
        <p class="mb-0 text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-finvera fw-bold text-decoration-none">Daftar di sini</a></p>
    </div>
</form>

@push('scripts')
<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
