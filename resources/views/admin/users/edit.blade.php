@extends('layouts.dashboard')

@section('page_title', 'Edit Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold mb-4">Edit Data Admin: {{ $user->name }}</h6>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Level Akses</label>
                            <select name="admin_level" class="form-select" required>
                                <option value="staff" {{ $user->admin_level == 'staff' ? 'selected' : '' }}>Pegawai</option>
                                <option value="master" {{ $user->admin_level == 'master' ? 'selected' : '' }}>Admin Utama</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Nomor HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Ubah Password (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-finvera px-4 fw-bold">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
