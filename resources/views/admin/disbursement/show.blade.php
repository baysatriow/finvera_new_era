@extends('layouts.dashboard')

@section('page_title', 'Detail Pembayaran')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Foto Aset Center & Contain */
    .doc-img-container {
        height: 180px;
        width: 100%;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .doc-img {
        max-height: 95%;
        max-width: 95%;
        object-fit: contain;
        transition: transform 0.3s;
    }
    .doc-img-container:hover .doc-img { transform: scale(1.05); }

    /* Tombol Overlay */
    .doc-overlay-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.6);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .doc-img-container:hover .doc-overlay-btn { opacity: 1; }

    /* Button Outline Fix */
    .btn-outline-custom {
        border: 1px solid #3A6D48;
        color: #3A6D48;
        background-color: white;
        font-weight: 600;
    }
    .btn-outline-custom:hover {
        background-color: #3A6D48;
        color: white;
    }

    .btn-back-custom {
        background-color: white;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-back-custom:hover {
        background-color: #f8f9fa;
        color: #3A6D48;
        border-color: #3A6D48;
        transform: translateX(-3px);
    }
</style>
@endpush

<div class="row g-4">
    <!-- Header Back Button -->
    <div class="col-12">
        <a href="{{ route('admin.disbursement.index') }}" class="btn-back-custom">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- STATISTIK RINGKAS (HEADER) -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                    <div class="card-body p-4">
                        <small class="text-white-50 text-uppercase fw-bold">Total Pinjaman</small>
                        <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</h3>
                        <div class="mt-2 small opacity-75 font-monospace">Kode: {{ $loan->loan_code }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <small class="text-muted text-uppercase fw-bold">Sudah Dibayar</small>
                        <h3 class="fw-bold mb-0 mt-1 text-success">Rp {{ number_format($paidAmount, 0, ',', '.') }}</h3>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <small class="text-muted text-uppercase fw-bold">Sisa Tagihan</small>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</h3>
                        <div class="mt-2 small text-muted">
                            Jatuh Tempo: {{ $loan->due_date->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO PEMINJAM & ASET -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Informasi Peminjam</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light rounded-circle p-3 me-3 text-finvera fw-bold fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{ substr($loan->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $loan->user->name }}</h5>
                        <div class="small text-muted">{{ $loan->user->email }}</div>
                    </div>
                </div>

                <ul class="list-group list-group-flush small mb-3">
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Nomor HP</span>
                        <span class="fw-bold text-dark">{{ $loan->user->phone }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Pekerjaan</span>
                        <span class="fw-bold text-dark">{{ $loan->user->job ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0">
                        <span class="text-muted d-block mb-1">Alamat</span>
                        <span class="fw-bold d-block lh-sm text-dark">{{ $loan->user->address_full ?? '-' }}</span>
                    </li>
                </ul>

                <div class="d-grid">
                    <a href="{{ route('admin.borrowers.show', $loan->user_id) }}" class="btn btn-outline-custom btn-sm rounded-pill fw-bold py-2">
                        Lihat Profil Lengkap <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Asset Preview -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Aset Jaminan</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Jenis Aset</small>
                        <strong class="text-dark">{{ $loan->application->asset_type ?? 'Aset' }}</strong>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Nilai</small>
                        <strong class="text-dark">Rp {{ number_format($loan->application->asset_value ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                         <label class="small text-muted mb-1 fw-bold">Dokumen</label>
                         @if($loan->application && $loan->application->asset_document_path)
                            <div class="doc-img-container">
                                <img src="{{ asset('storage/' . $loan->application->asset_document_path) }}" class="doc-img" alt="Dokumen">
                                <a href="{{ asset('storage/' . $loan->application->asset_document_path) }}" target="_blank" class="doc-overlay-btn"><i class="fas fa-expand-alt"></i></a>
                            </div>
                        @else
                            <div class="doc-img-container text-muted small">No Image</div>
                        @endif
                    </div>
                     <div class="col-6">
                         <label class="small text-muted mb-1 fw-bold">Selfie</label>
                         @if($loan->application && $loan->application->asset_selfie_path)
                            <div class="doc-img-container">
                                <img src="{{ asset('storage/' . $loan->application->asset_selfie_path) }}" class="doc-img" alt="Selfie">
                                <a href="{{ asset('storage/' . $loan->application->asset_selfie_path) }}" target="_blank" class="doc-overlay-btn"><i class="fas fa-expand-alt"></i></a>
                            </div>
                        @else
                            <div class="doc-img-container text-muted small">No Image</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL CICILAN -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Jadwal Cicilan</h5>
                @if($loan->status == 'paid')
                    <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-double me-1"></i> LUNAS TOTAL</span>
                @else
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Aktif</span>
                @endif
            </div>
            <div class="card-body px-4 pt-0 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="disbursementInstallmentTable">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Ke-</th>
                                <th>Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th>Denda</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loan->installments as $ins)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $ins->installment_number }}</td>
                                <td>
                                    <span class="{{ $ins->status != 'paid' && $ins->due_date < now() ? 'text-danger fw-bold' : '' }}">
                                        {{ $ins->due_date->format('d M Y') }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($ins->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($ins->tazir_amount > 0)
                                        <span class="text-danger small">+{{ number_format($ins->tazir_amount, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">
                                    Rp {{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($ins->status == 'waiting')
                                        <span class="badge bg-info text-white rounded-pill px-3">Menunggu Verifikasi</span>
                                    @elseif($ins->status == 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                                    @elseif($ins->status == 'late')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Terlambat</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ ucfirst($ins->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($ins->status == 'waiting')
                                        <!-- Tombol Lihat Bukti & Verifikasi -->
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $ins->id }}">
                                            <i class="fas fa-search me-1"></i> Cek Bukti
                                        </button>

                                        <!-- Modal Verifikasi -->
                                        <div class="modal fade" id="verifyModal{{ $ins->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow rounded-4">
                                                    <div class="modal-header border-bottom-0">
                                                        <h6 class="fw-bold text-dark">Verifikasi Pembayaran Bulan {{ $ins->installment_number }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        @if($ins->proof_path)
                                                            <div class="border rounded bg-light p-1 mb-3">
                                                                <img src="{{ asset('storage/' . $ins->proof_path) }}" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
                                                            </div>
                                                            <a href="{{ asset('storage/' . $ins->proof_path) }}" target="_blank" class="btn btn-sm btn-light border mb-2">Lihat Ukuran Asli</a>
                                                        @else
                                                            <div class="alert alert-warning">Tidak ada bukti foto.</div>
                                                        @endif

                                                        <p class="small text-muted mt-2">
                                                            User mengklaim bayar pada: <strong class="text-dark">{{ $ins->paid_at ? $ins->paid_at->format('d M Y') : '-' }}</strong>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer justify-content-center border-top-0 pb-4 pt-0">
                                                        <form action="{{ route('admin.disbursement.verify', $ins->id) }}" method="POST" class="d-flex gap-2 w-100 px-3">
                                                            @csrf
                                                            <button type="submit" name="action" value="reject" class="btn btn-outline-danger rounded-pill fw-bold flex-fill py-2">
                                                                <i class="fas fa-times me-1"></i> Tolak
                                                            </button>
                                                            <button type="submit" name="action" value="approve" class="btn btn-success rounded-pill fw-bold flex-fill py-2">
                                                                <i class="fas fa-check me-1"></i> Terima & Lunas
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($ins->status == 'paid')
                                         <span class="text-success small fw-bold"><i class="fas fa-check-double"></i> Verified</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
