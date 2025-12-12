@extends('layouts.dashboard')

@section('page_title', 'Tambah Pengguna Admin')

@section('content')
<style>
    .form-control, .form-select {
        height: 50px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3A6D48;
        box-shadow: 0 0 0 0.2rem rgba(58, 109, 72, 0.1);
    }
    .btn-finvera-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        transition: all 0.3s;
    }
    .btn-finvera-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(58, 109, 72, 0.3);
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-4 px-4 border-bottom-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-user-plus fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Form Pengguna Baru</h5>
                        <p class="text-muted small mb-0">Tambahkan admin atau pegawai baru ke dalam sistem.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-2">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Level Akses</label>
                            <select name="admin_level" class="form-select" required>
                                <option value="staff">Pegawai (Akses Terbatas)</option>
                                <option value="master">Admin Utama (Full Akses)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Username Login</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-at text-muted"></i></span>
                                <input type="text" name="username" class="form-control border-start-0 ps-0" required placeholder="username_admin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin@finvera.com">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nomor HP</label>
                            <input type="text" name="phone" class="form-control" required placeholder="0812...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Password Awal</label>
                            <input type="text" name="password" class="form-control" required value="password123">
                            <div class="form-text small text-warning"><i class="fas fa-info-circle me-1"></i> Default: password123 (Harap segera diganti)</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-muted border">Batal</a>
                        <button type="submit" class="btn btn-finvera-solid px-5 py-2 rounded-pill fw-bold shadow-sm">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
