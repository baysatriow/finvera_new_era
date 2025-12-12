@extends('layouts.dashboard')

@section('page_title', 'Rekening Bank')

@section('content')
<style>
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

    .transition-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Daftar Rekening</h5>
                    <p class="text-muted small mb-0">Kelola rekening pencairan dana Anda.</p>
                </div>
                <a href="{{ route('bank.create') }}" class="btn btn-finvera-solid rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus me-2"></i> Tambah
                </a>
            </div>

            <div class="card-body p-4 pt-0">
                @if($accounts->isEmpty())
                    <div class="text-center py-5 border rounded-4 bg-light">
                        <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 shadow-sm text-muted">
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Belum Ada Rekening</h6>
                        <p class="text-muted small mb-3">Tambahkan rekening untuk pencairan pinjaman.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($accounts as $acc)
                        <div class="col-12">
                            <div class="card border {{ $acc->is_primary ? 'border-success bg-success bg-opacity-10' : 'border-light bg-white' }} shadow-sm rounded-4 h-100 transition-hover">
                                <div class="card-body d-flex align-items-center justify-content-between p-4">

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white rounded-3 p-3 shadow-sm text-finvera text-center" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-university fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold text-dark mb-0">{{ $acc->bank_name }}</h6>
                                                @if($acc->is_primary)
                                                    <span class="badge bg-success rounded-pill px-2" style="font-size: 0.65rem;">UTAMA</span>
                                                @endif
                                            </div>
                                            <div class="font-monospace text-dark fs-5 my-1">{{ $acc->account_number }}</div>
                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">{{ $acc->account_holder_name }}</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if(!$acc->is_primary)
                                            <form action="{{ route('bank.primary', $acc->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm text-success" title="Jadikan Utama" data-bs-toggle="tooltip">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('bank.edit', $acc->id) }}" class="btn btn-light btn-sm rounded-circle shadow-sm text-primary" title="Edit" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm text-danger delete-btn" data-id="{{ $acc->id }}" title="Hapus" data-bs-toggle="tooltip">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <form id="delete-form-{{ $acc->id }}" action="{{ route('bank.destroy', $acc->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Rekening Pencairan</h6>
                </div>
                <p class="small text-muted mb-0 text-justify lh-base">
                    Rekening yang Anda tandai sebagai <span class="badge bg-success rounded-pill" style="font-size: 0.6rem;">UTAMA</span> akan otomatis digunakan sistem sebagai tujuan transfer saat pengajuan pinjaman Anda disetujui.
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Keamanan Data</h6>
                </div>
                <p class="small opacity-90 mb-0 text-justify lh-base">
                    Demi keamanan, nama pemilik rekening <strong>HARUS SAMA</strong> dengan nama pada KTP Anda. Pencairan akan gagal jika data tidak cocok.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Hapus Rekening?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
