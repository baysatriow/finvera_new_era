@extends('layouts.dashboard')

@section('page_title', 'Manajemen Pencairan & Pembayaran')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Button Action */
    .btn-action-outline {
        border: 1px solid #dee2e6;
        color: #3A6D48;
        background-color: white;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-action-outline:hover {
        border-color: #3A6D48;
        background-color: #f4fcf6;
        color: #2c5236;
        transform: translateY(-1px);
    }
</style>
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0">
        <h5 class="fw-bold mb-0 text-dark">Monitoring Pinjaman Aktif</h5>
        <p class="text-muted small mb-0">Pantau status pencairan dan progres pembayaran cicilan nasabah.</p>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="disbursementTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 rounded-start">ID & Tanggal Cair</th>
                        <th>Peminjam</th>
                        <th>Total Pinjaman</th>
                        <th style="width: 25%;">Progress Pelunasan</th>
                        <th>Status</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border mb-1 font-monospace">{{ $loan->loan_code }}</span>
                            <div class="small text-muted">{{ $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $loan->user->name }}</div>
                            <div class="small text-muted">Tenor: {{ $loan->application->tenor ?? '-' }} Bulan</div>
                        </td>
                        <td class="fw-bold text-dark">Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $paid = $loan->total_amount - $loan->remaining_balance;
                                $percent = ($paid / $loan->total_amount) * 100;
                                $percent = min(100, max(0, $percent));
                            @endphp
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted" style="font-size: 0.7rem;">Sisa: Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</span>
                                <span class="fw-bold text-success" style="font-size: 0.7rem;">{{ round($percent) }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                        </td>
                        <td>
                            @if($loan->status == 'active')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Aktif</span>
                            @elseif($loan->status == 'paid')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                            @elseif($loan->status == 'past_due')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Terlambat</span>
                            @elseif($loan->status == 'default')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Macet</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.disbursement.show', $loan->id) }}" class="btn btn-sm btn-action-outline rounded-pill px-3">
                                Detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
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
<script>
    $(document).ready(function() {
        $('#disbursementTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[ 0, "desc" ]]
        });
    });
</script>
@endpush
@endsection
