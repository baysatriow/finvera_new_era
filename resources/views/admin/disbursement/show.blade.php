@extends('layouts.dashboard')

@section('page_title', 'Detail Pembayaran')

@section('content')
<div class="row g-4">
    <!-- STATISTIK RINGKAS (HEADER) -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                    <div class="card-body p-4">
                        <small class="text-white-50 text-uppercase fw-bold">Total Pinjaman</small>
                        <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</h3>
                        <div class="mt-2 small opacity-75">Kode: {{ $loan->loan_code }}</div>
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

    <!-- INFO PEMINJAM -->
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

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Nomor HP</span>
                        <span class="fw-bold">{{ $loan->user->phone }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                        <span class="text-muted">Pekerjaan</span>
                        <span class="fw-bold">{{ $loan->user->job ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 border-0">
                        <span class="text-muted d-block mb-1">Alamat</span>
                        <span class="fw-bold d-block lh-sm">{{ $loan->user->address_full ?? '-' }}</span>
                    </li>
                </ul>

                <div class="d-grid mt-3">
                    <a href="{{ route('admin.borrowers.show', $loan->user_id) }}" class="btn btn-outline-finvera btn-sm rounded-pill">
                        Lihat Profil Lengkap
                    </a>
                </div>
            </div>
        </div>

        <!-- Asset Preview Mini -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Aset Jaminan</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="p-3 bg-light rounded-3 mb-3">
                    <small class="text-muted d-block">Jenis Aset</small>
                    <strong>{{ $loan->application->asset_type ?? 'Aset' }}</strong>
                </div>
                @if($loan->application && $loan->application->asset_document_path)
                    <img src="{{ asset('storage/' . $loan->application->asset_document_path) }}" class="img-fluid rounded border w-100" alt="Aset">
                @endif
            </div>
        </div>
    </div>

    <!-- TABEL CICILAN -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Jadwal Cicilan</h5>
                @if($loan->status == 'paid')
                    <span class="badge bg-success px-3 py-2"><i class="fas fa-check-double me-1"></i> LUNAS TOTAL</span>
                @else
                    <span class="badge bg-primary px-3 py-2">Aktif</span>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Ke-</th>
                            <th>Jatuh Tempo</th>
                            <th>Nominal</th>
                            <th>Denda (Ta'zir)</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->installments as $ins)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $ins->installment_number }}</td>
                            <td>
                                <span class="{{ $ins->status != 'paid' && $ins->due_date < now() ? 'text-danger fw-bold' : '' }}">
                                    {{ $ins->due_date->format('d M Y') }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($ins->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($ins->tazir_amount > 0)
                                    <span class="text-danger small">+ Rp {{ number_format($ins->tazir_amount, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-bold">
                                Rp {{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($ins->status == 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Lunas</span>
                                @elseif($ins->status == 'late')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Terlambat</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Belum</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($ins->paid_at)
                                    <div class="small text-success fw-bold">{{ $ins->paid_at->format('d/m/y') }}</div>
                                    <div class="small text-muted">{{ $ins->paid_at->format('H:i') }}</div>
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
@endsection
