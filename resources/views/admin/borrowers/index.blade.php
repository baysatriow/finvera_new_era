@extends('layouts.dashboard')

@section('page_title', 'Data Peminjam')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
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
        <h5 class="fw-bold mb-0 text-dark">Database Peminjam</h5>
        <p class="text-muted small mb-0">Kelola dan pantau seluruh data nasabah yang terdaftar.</p>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="borrowersTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 rounded-start">Peminjam</th>
                        <th>Kontak</th>
                        <th>Pekerjaan</th>
                        <th>Credit Score</th>
                        <th>Status KYC</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrowers as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="small text-muted">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="small text-dark fw-bold">{{ $user->phone }}</span>
                                <span class="small text-muted">{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->job)
                                <span class="d-block text-dark small fw-bold">{{ $user->job }}</span>
                                <span class="d-block text-muted small" style="font-size: 0.7rem;">Rp {{ number_format($user->monthly_income, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted small fst-italic">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->credit_score > 0)
                                <span class="badge bg-light text-dark border fw-bold">{{ $user->credit_score }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->kyc_status == 'verified')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Verified</span>
                            @elseif($user->kyc_status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Unverified</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.borrowers.show', $user->id) }}" class="btn btn-sm btn-action-outline rounded-pill px-3">
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
        $('#borrowersTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[ 0, "asc" ]]
        });
    });
</script>
@endpush
@endsection
