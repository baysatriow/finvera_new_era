@extends('layouts.dashboard')

@section('page_title', 'Detail Peminjam')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
    }
    .nav-tabs .nav-link.active {
        color: #3A6D48;
        border-bottom: 2px solid #3A6D48;
        background: transparent;
    }
    .nav-tabs .nav-link:hover { border-color: transparent; color: #3A6D48; }
    .kyc-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #eee;
        transition: transform 0.3s;
    }
    .kyc-img:hover { transform: scale(1.02); }
</style>
@endpush

<div class="row">
    <!-- Header Profil -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-finvera text-white rounded-circle fs-2 fw-bold d-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px;">
                        {{ substr($borrower->name, 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1">{{ $borrower->name }}</h4>
                        <div class="text-muted mb-2">{{ $borrower->email }} &bull; {{ $borrower->phone }}</div>
                        <div class="d-flex gap-2">
                            @if($borrower->kyc_status == 'verified')
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> KYC Verified</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> KYC {{ ucfirst($borrower->kyc_status) }}</span>
                            @endif
                            <span class="badge bg-light text-dark border">
                                Credit Score: {{ $borrower->credit_score > 0 ? $borrower->credit_score : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Bergabung Sejak</small>
                        <span class="fw-bold">{{ $borrower->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-0 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="borrowerTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Data Pribadi</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="kyc-tab" data-bs-toggle="tab" data-bs-target="#kyc" type="button">Dokumen KYC</button>
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
                                <h6 class="fw-bold text-finvera mb-3"><i class="fas fa-briefcase me-2"></i>Pekerjaan & Finansial</h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="text-muted w-50">Pekerjaan</td>
                                        <td class="fw-bold">{{ $borrower->job ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pendapatan Bulanan</td>
                                        <td class="fw-bold">Rp {{ number_format($borrower->monthly_income, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Lama Bekerja</td>
                                        <td class="fw-bold">{{ $borrower->employment_duration }} Bulan</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-finvera mb-3"><i class="fas fa-map-marker-alt me-2"></i>Alamat Domisili</h6>
                                <p class="mb-1 fw-bold">{{ $borrower->address_full ?? '-' }}</p>
                                <p class="text-muted small mb-0">
                                    {{ $borrower->village }}, {{ $borrower->district }}<br>
                                    {{ $borrower->city }}, {{ $borrower->province }} {{ $borrower->postal_code }}
                                </p>
                            </div>
                            <div class="col-12">
                                <hr class="border-light my-2">
                                <h6 class="fw-bold text-finvera mb-3 mt-3"><i class="fas fa-university me-2"></i>Rekening Bank Terdaftar</h6>
                                @if($borrower->bankAccounts->isNotEmpty())
                                    <div class="row g-3">
                                        @foreach($borrower->bankAccounts as $bank)
                                            <div class="col-md-4">
                                                <div class="p-3 border rounded-3 bg-light {{ $bank->is_primary ? 'border-success border-2' : '' }}">
                                                    <div class="fw-bold">{{ $bank->bank_name }}</div>
                                                    <div class="font-monospace fs-5">{{ $bank->account_number }}</div>
                                                    <small class="text-muted text-uppercase">{{ $bank->account_holder_name }}</small>
                                                    @if($bank->is_primary) <span class="badge bg-success float-end mt-1">Utama</span> @endif
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

                    <!-- TAB 2: DOKUMEN KYC -->
                    <div class="tab-pane fade" id="kyc" role="tabpanel">
                        @if($borrower->kyc)
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info d-inline-block border-0 shadow-sm">
                                        <strong>NIK Terdaftar:</strong> {{ $borrower->kyc->nik }}
                                        @if($borrower->kyc_status == 'verified') <i class="fas fa-check-circle text-success ms-2"></i> @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted mb-2">Foto KTP</label>
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $borrower->kyc->ktp_image_path) }}" class="kyc-img shadow-sm" alt="KTP">
                                        <a href="{{ asset('storage/' . $borrower->kyc->ktp_image_path) }}" target="_blank" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-3 opacity-75"><i class="fas fa-expand"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted mb-2">Foto Selfie</label>
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $borrower->kyc->selfie_image_path) }}" class="kyc-img shadow-sm" alt="Selfie">
                                        <a href="{{ asset('storage/' . $borrower->kyc->selfie_image_path) }}" target="_blank" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-3 opacity-75"><i class="fas fa-expand"></i></a>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="fw-bold text-muted">Data Hasil AI</label>
                                    <pre class="bg-light p-3 rounded border text-muted small mt-2">{{ json_encode($borrower->kyc->ocr_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-id-card fa-3x mb-3 opacity-50"></i>
                                <p>Pengguna belum melakukan verifikasi KYC.</p>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 3: RIWAYAT PINJAMAN -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100" id="historyTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Tanggal</th>
                                        <th>ID</th>
                                        <th>Nominal</th>
                                        <th>Tenor</th>
                                        <th>Tujuan</th>
                                        <th>Status</th>
                                        <th>Skor AI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($borrower->applications as $app)
                                    <tr>
                                        <td class="ps-3">{{ $app->created_at->format('d M Y') }}</td>
                                        <td>#{{ str_pad($app->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td class="fw-bold text-dark">Rp {{ number_format($app->amount, 0, ',', '.') }}</td>
                                        <td>{{ $app->tenor }} Bulan</td>
                                        <td>{{ Str::limit($app->purpose, 30) }}</td>
                                        <td>
                                            @php
                                                $bg = match($app->status) {
                                                    'approved' => 'success', 'pending' => 'warning',
                                                    'rejected' => 'danger', 'paid' => 'primary', default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $bg }} bg-opacity-10 text-{{ $bg }} rounded-pill">{{ ucfirst($app->status) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $app->ai_score >= 70 ? 'text-success' : 'text-danger' }}">
                                                {{ $app->ai_score }}
                                            </span>
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
