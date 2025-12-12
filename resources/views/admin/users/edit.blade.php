@extends('layouts.dashboard')

@section('page_title', 'Edit Pengguna')

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
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-user-edit fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Edit Data Pengguna</h5>
                        <p class="text-muted small mb-0">Perbarui informasi akun untuk <strong>{{ $user->name }}</strong>.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-2">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Username tidak diedit, hanya ditampilkan sebagai info (optional) -->
                    <div class="alert alert-light border border-light rounded-3 d-flex align-items-center px-3 py-2 mb-4">
                        <i class="fas fa-user-tag text-muted me-2"></i>
                        <small class="text-muted">Username: <strong>{{ $user->username }}</strong></small>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Level Akses</label>
                            <select name="admin_level" class="form-select" required>
                                <option value="staff" {{ $user->admin_level == 'staff' ? 'selected' : '' }}>Pegawai</option>
                                <option value="master" {{ $user->admin_level == 'master' ? 'selected' : '' }}>Admin Utama</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nomor HP</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                        </div>
                    </div>

                    <div class="alert alert-light border border-secondary border-opacity-10 rounded-3 p-3 mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-lock me-2 text-warning"></i>
                            <label class="form-label fw-bold text-dark mb-0">Ubah Password (Opsional)</label>
                        </div>
                        <input type="password" name="password" class="form-control bg-white" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-muted border">Batal</a>
                        <button type="submit" class="btn btn-finvera-solid px-5 py-2 rounded-pill fw-bold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
