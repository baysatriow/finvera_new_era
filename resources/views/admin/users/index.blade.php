@extends('layouts.dashboard')

@section('page_title', 'Manajemen Pengguna')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .btn-finvera-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        transition: all 0.3s;
        font-weight: 600;
    }
    .btn-finvera-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(58, 109, 72, 0.3);
    }

    /* Custom Table Styles */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 6px 15px;
        border: 1px solid #dee2e6;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 5px 30px 5px 15px;
        border: 1px solid #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #3A6D48;
        border-color: #3A6D48;
        color: white;
    }
    .page-link { color: #3A6D48; }
    .page-link:hover { color: #2c5236; }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Daftar Admin & Pegawai</h5>
            <p class="text-muted small mb-0">Kelola akses pengguna sistem internal.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-finvera-solid rounded-pill px-4 shadow-sm">
            <i class="fas fa-user-plus me-2"></i> Tambah Baru
        </a>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="usersTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 rounded-start">Nama</th>
                        <th>Email & Username</th>
                        <th>Role / Level</th>
                        <th>No. Telepon</th>
                        <th>Terdaftar</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <span class="fw-bold text-dark">{{ $admin->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small fw-bold">{{ $admin->email }}</div>
                            <div class="text-muted small">@ {{ $admin->username }}</div>
                        </td>
                        <td>
                            @if($admin->admin_level == 'master')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                    <i class="fas fa-crown me-1"></i> Utama
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">
                                    <i class="fas fa-user-tie me-1"></i> Pegawai
                                </span>
                            @endif
                        </td>
                        <td>{{ $admin->phone }}</td>
                        <td class="small text-muted">{{ $admin->created_at->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-sm btn-light border rounded-circle shadow-sm text-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($admin->id !== Auth::id())
                                    <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle shadow-sm text-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            columnDefs: [{ orderable: false, targets: 5 }]
        });

        // SWAL Delete Confirmation
        $('.btn-delete').click(function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Akses login pengguna ini akan dicabut permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
