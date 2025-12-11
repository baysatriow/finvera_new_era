@extends('layouts.dashboard')

@section('page_title', 'Tambah Pengguna Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Form Pengguna Baru</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama Admin">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Level Akses</label>
                            <select name="admin_level" class="form-select" required>
                                <option value="staff">Pegawai (Terbatas)</option>
                                <option value="master">Admin Utama (Full Akses)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Username Login</label>
                            <input type="text" name="username" class="form-control" required placeholder="username_admin">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="admin@finvera.com">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nomor HP</label>
                            <input type="text" name="phone" class="form-control" required placeholder="0812...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Password Default</label>
                            <input type="text" name="password" class="form-control" required value="password123">
                            <div class="form-text small">Default: password123 (Harap segera diganti)</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-finvera px-4 fw-bold">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
