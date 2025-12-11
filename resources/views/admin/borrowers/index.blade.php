@extends('layouts.dashboard')

@section('page_title', 'Data Peminjam')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-4 px-4 border-bottom-0">
        <h5 class="fw-bold mb-0 text-dark">Database Peminjam</h5>
        <p class="text-muted small mb-0">Kelola dan pantau seluruh data nasabah yang terdaftar.</p>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="borrowersTable">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 rounded-start">Peminjam</th>
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
                                <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera" style="width: 40px; height: 40px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="small text-muted">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark">{{ $user->email }}</div>
                            <div class="small text-muted">{{ $user->phone }}</div>
                        </td>
                        <td>
                            @if($user->job)
                                <span class="d-block text-dark small">{{ $user->job }}</span>
                                <span class="d-block text-muted small" style="font-size: 0.75rem;">Gaji: Rp {{ number_format($user->monthly_income/1000000, 1) }} Juta</span>
                            @else
                                <span class="text-muted small fst-italic">Belum Lengkap</span>
                            @endif
                        </td>
                        <td>
                            @if($user->credit_score > 0)
                                <span class="fw-bold {{ $user->credit_score >= 70 ? 'text-success' : 'text-danger' }}">
                                    {{ $user->credit_score }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->kyc_status == 'verified')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Verified</span>
                            @elseif($user->kyc_status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Unverified</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.borrowers.show', $user->id) }}" class="btn btn-sm btn-outline-finvera rounded-pill px-3">
                                <i class="fas fa-eye me-1"></i> Detail
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
