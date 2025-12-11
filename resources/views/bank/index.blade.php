@extends('layouts.dashboard')

@section('page_title', 'Data Rekening Bank')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Rekening Pencairan</h5>
                        <p class="text-muted small mb-0">Dana pinjaman akan ditransfer ke rekening utama.</p>
                    </div>
                    <a href="{{ route('bank.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Rekening
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($accounts->isEmpty())
                    <div class="text-center py-4 border rounded-3 bg-light">
                        <i class="fas fa-university fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">Belum ada rekening terdaftar.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($accounts as $acc)
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 shadow-sm rounded-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                                    <i class="fas fa-credit-card fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $acc->bank_name }}</h6>
                                    <div class="text-dark font-monospace">{{ $acc->account_number }}</div>
                                    <small class="text-muted text-uppercase">{{ $acc->account_holder_name }}</small>
                                    @if($acc->is_primary)
                                        <span class="badge bg-success ms-2">Utama</span>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('bank.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="alert alert-info border-0 shadow-sm">
            <h6 class="fw-bold"><i class="fas fa-shield-alt me-2"></i>Info Keamanan</h6>
            <p class="small mb-0 text-justify">
                Demi keamanan transaksi dan mencegah pencucian uang, nama pemilik rekening <strong>HARUS SAMA</strong> dengan nama pada KTP Anda. Jika berbeda, proses pencairan akan gagal.
            </p>
        </div>
    </div>
</div>
@endsection
