@extends('layouts.dashboard')

@section('page_title', 'Detail Peminjam')

@section('content')
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .nav-tabs {
        border-bottom: 2px solid #f1f3f5;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
        transition: all 0.2s;
        margin-bottom: -2px;
    }
    .nav-tabs .nav-link:hover {
        color: #3A6D48;
        background: transparent;
        border-bottom-color: #d1e7dd;
    }
    .nav-tabs .nav-link.active {
        color: #3A6D48;
        background: transparent;
        border-bottom: 2px solid #3A6D48;
    }

    .kyc-img-container {
        height: 250px;
        width: 100%;
        background-color: #333;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .kyc-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .ai-data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
    .ai-data-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #eee;
    }
</style>
@endpush

<div class="row g-4">
    <div class="col-12">
        <a href="{{ route('admin.borrowers.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center flex-wrap gap-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle fs-1 fw-bold d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        {{ substr($borrower->name, 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1">{{ $borrower->name }}</h4>
                        <div class="text-muted d-flex align-items-center gap-3 mb-2 flex-wrap">
                            <span><i class="fas fa-envelope me-1"></i> {{ $borrower->email }}</span>
                            <span><i class="fas fa-phone me-1"></i> {{ $borrower->phone }}</span>
                            <span><i class="fas fa-id-badge me-1"></i> ID: #{{ str_pad($borrower->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            @if($borrower->kyc_status == 'verified')
                                <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Verified</span>
                            @elseif($borrower->kyc_status == 'pending')
                                <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-clock me-1"></i> Pending</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3"><i class="fas fa-times-circle me-1"></i> Unverified</span>
                            @endif

                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                Credit Score: <strong>{{ $borrower->credit_score > 0 ? $borrower->credit_score : 'N/A' }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Total Pinjaman Aktif</small>
                        <h3 class="fw-bold text-success mb-0">
                            Rp {{ number_format($borrower->loans()->where('status', 'active')->sum('remaining_balance'), 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                <ul class="nav nav-tabs" id="borrowerTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Data Pribadi</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="kyc-tab" data-bs-toggle="tab" data-bs-target="#kyc" type="button">Dokumen & AI</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">Riwayat Pinjaman</button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="borrowerTabsContent">

                    <!-- TAB 1: DATA PRIBADI -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Pekerjaan & Finansial</h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted w-50">Pekerjaan</td>
                                        <td class="fw-bold text-dark">{{ $borrower->job ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pendapatan Bulanan</td>
                                        <td class="fw-bold text-success">Rp {{ number_format($borrower->monthly_income, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Lama Bekerja</td>
                                        <td class="fw-bold text-dark">{{ $borrower->employment_duration }} Bulan</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Alamat Domisili</h6>
                                <p class="mb-1 fw-bold text-dark">{{ $borrower->address_full ?? '-' }}</p>
                                <p class="text-muted small mb-0 text-uppercase">
                                    {{ $borrower->village }}, {{ $borrower->district }}<br>
                                    {{ $borrower->city }}, {{ $borrower->province }} {{ $borrower->postal_code }}
                                </p>
                            </div>
                            <div class="col-12">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 mt-2">Rekening Bank Terdaftar</h6>
                                @if($borrower->bankAccounts->isNotEmpty())
                                    <div class="row g-3">
                                        @foreach($borrower->bankAccounts as $bank)
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light {{ $bank->is_primary ? 'border-success border-2' : '' }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark">{{ $bank->bank_name }}</span>
                                                        @if($bank->is_primary) <span class="badge bg-success">Utama</span> @endif
                                                    </div>
                                                    <div class="font-monospace fs-5 text-dark">{{ $bank->account_number }}</div>
                                                    <small class="text-muted text-uppercase">{{ $bank->account_holder_name }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted fst-italic">Belum ada rekening terdaftar.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: DOKUMEN KYC & AI -->
                    <div class="tab-pane fade" id="kyc" role="tabpanel">
                        @if($borrower->kyc)
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-header bg-transparent border-0 fw-bold text-dark">
                                            <i class="fas fa-robot me-2 text-primary"></i>Analisis Verifikasi AI
                                        </div>
                                        <div class="card-body pt-0">
                                            @php
                                                $ocr = $borrower->kyc->ocr_data;
                                                if(is_string($ocr)) $ocr = json_decode($ocr, true);
                                            @endphp

                                            <div class="ai-data-grid">
                                                <div class="ai-data-item">
                                                    <small class="text-muted d-block">NIK Terbaca</small>
                                                    <strong>{{ $ocr['nik'] ?? '-' }}</strong>
                                                </div>
                                                <div class="ai-data-item">
                                                    <small class="text-muted d-block">Nama Terbaca</small>
                                                    <strong>{{ $ocr['name'] ?? '-' }}</strong>
                                                </div>
                                                <div class="ai-data-item">
                                                    <small class="text-muted d-block">Face Match Score</small>
                                                    <strong class="{{ ($ocr['face_match_score'] ?? 0) >= 75 ? 'text-success' : 'text-danger' }}">
                                                        {{ $ocr['face_match_score'] ?? 0 }}%
                                                    </strong>
                                                </div>
                                                <div class="ai-data-item">
                                                    <small class="text-muted d-block">Status Validitas</small>
                                                    <strong class="{{ ($ocr['is_valid'] ?? false) ? 'text-success' : 'text-danger' }}">
                                                        {{ ($ocr['is_valid'] ?? false) ? 'VALID' : 'INVALID' }}
                                                    </strong>
                                                </div>
                                            </div>
                                            @if(isset($ocr['reason']))
                                                <div class="mt-3 small text-muted fst-italic">
                                                    Catatan: "{{ $ocr['reason'] }}"
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold text-muted mb-2 small text-uppercase">Foto KTP</label>
                                    <div class="kyc-img-container">
                                        <img src="{{ asset('storage/' . $borrower->kyc->ktp_image_path) }}" class="kyc-img" alt="KTP">
                                        <a href="{{ asset('storage/' . $borrower->kyc->ktp_image_path) }}" target="_blank" class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-3 opacity-75 fw-bold shadow-sm">
                                            <i class="fas fa-expand me-1"></i> Full View
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted mb-2 small text-uppercase">Foto Selfie</label>
                                    <div class="kyc-img-container">
                                        <img src="{{ asset('storage/' . $borrower->kyc->selfie_image_path) }}" class="kyc-img" alt="Selfie">
                                        <a href="{{ asset('storage/' . $borrower->kyc->selfie_image_path) }}" target="_blank" class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-3 opacity-75 fw-bold shadow-sm">
                                            <i class="fas fa-expand me-1"></i> Full View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                    <i class="fas fa-id-card fa-3x opacity-50"></i>
                                </div>
                                <p class="mb-0">Pengguna belum melakukan verifikasi KYC.</p>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 3: RIWAYAT PINJAMAN -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="historyTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-3 py-3">Tanggal</th>
                                        <th>ID</th>
                                        <th>Nominal</th>
                                        <th>Tenor</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($borrower->applications as $app)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">{{ $app->created_at->format('d M Y') }}</td>
                                        <td><span class="badge bg-light text-dark border">#{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                        <td class="fw-bold text-dark">Rp {{ number_format($app->amount, 0, ',', '.') }}</td>
                                        <td>{{ $app->tenor }} Bln</td>
                                        <td>
                                            @php
                                                $bg = match($app->status) {
                                                    'approved' => 'success', 'pending' => 'warning', 'active' => 'primary',
                                                    'rejected' => 'danger', 'paid' => 'success', default => 'secondary'
                                                };
                                                $label = ucfirst($app->status);
                                                if($app->status == 'approved') $label = 'Disetujui';
                                                if($app->status == 'active') $label = 'Aktif';
                                            @endphp
                                            <span class="badge bg-{{ $bg }} bg-opacity-10 text-{{ $bg }} rounded-pill px-3">{{ $label }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            @if($app->loan)
                                                <a href="{{ route('admin.disbursement.show', $app->loan->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    Lihat Cicilan <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    Review <i class="fas fa-eye ms-1"></i>
                                                </a>
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
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[ 0, "desc" ]]
        });
    });
</script>
@endpush
@endsection
