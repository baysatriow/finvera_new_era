@extends('layouts.dashboard')

@section('page_title', 'Detail Pinjaman')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .detail-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        background: white;
        transition: transform 0.2s;
    }

    .status-badge {
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }

    .doc-img-box {
        height: 200px;
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
    .doc-img-box:hover .doc-img { transform: scale(1.05); }

    .doc-overlay-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
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
    .doc-img-box:hover .doc-overlay-btn { opacity: 1; }

    .ai-score-container {
        position: relative;
        margin: 0 auto;
        width: 110px;
        height: 110px;
    }
    @media (max-width: 768px) {
        .ai-score-container {
            width: 90px;
            height: 90px;
        }
    }

    .btn-pay-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 6px 16px;
    }
    .btn-pay-solid:hover {
        background-color: #2c5236;
        color: white;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        width: 6px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }
</style>
@endpush

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card detail-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Nomor Pengajuan</small>
                        <h5 class="fw-bold text-dark mb-0 font-monospace">#{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</h5>
                    </div>
                    @php
                        $statusClass = match($loan->status) {
                            'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', 'paid' => 'primary', default => 'secondary'
                        };
                        $statusLabel = match($loan->status) {
                            'approved' => 'Aktif', 'pending' => 'Menunggu Review', 'rejected' => 'Ditolak', 'paid' => 'Lunas', default => ucfirst($loan->status)
                        };
                    @endphp
                    <span class="status-badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 border-start border-4 border-success h-100">
                            <small class="text-muted d-block mb-1 fw-bold">Nominal Pinjaman</small>
                            <h4 class="fw-bold text-finvera mb-0">Rp {{ number_format($loan->amount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tenor</span>
                                <span class="fw-bold text-dark">{{ $loan->tenor }} Bulan</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Tanggal</span>
                                <span class="fw-bold text-dark">{{ $loan->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-white">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Tujuan Penggunaan</label>
                            <p class="mb-0 text-dark fst-italic">"{{ $loan->purpose }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-calendar-alt me-2 text-finvera"></i>
                    {{ $loan->status == 'pending' ? 'Estimasi Jadwal' : 'Jadwal Pembayaran' }}
                </h6>
                @if(isset($loan->loan))
                    <span class="badge bg-light text-dark border font-monospace">{{ $loan->loan->loan_code }}</span>
                @endif
            </div>

            <div class="card-body px-4 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="installmentTable">
                        <thead class="bg-light text-muted text-uppercase small">
                            <tr>
                                <th class="ps-3 py-3">Bln</th>
                                <th>Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th class="text-center">Status</th>
                                @if($loan->status != 'pending') <th class="text-end pe-3">Aksi</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($loan->status == 'pending' || $loan->status == 'rejected')
                                <!-- SIMULASI -->
                                @php
                                    $monthlyEst = ceil($loan->amount / $loan->tenor);
                                    $startDate = $loan->created_at;
                                @endphp
                                @for($i = 1; $i <= $loan->tenor; $i++)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted">{{ $i }}</td>
                                        <td class="text-muted">{{ $startDate->copy()->addMonths($i)->format('d M Y') }} <span class="badge bg-light text-muted ms-1">Est</span></td>
                                        <td class="fw-bold text-muted">Rp {{ number_format($monthlyEst, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Draft</span>
                                        </td>
                                    </tr>
                                @endfor
                            @elseif($loan->loan)
                                <!-- REAL DATA -->
                                @foreach($loan->loan->installments as $ins)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $ins->installment_number }}</td>
                                    <td>
                                        @if($ins->status != 'paid' && $ins->due_date < now())
                                            <span class="text-danger fw-bold">{{ $ins->due_date->format('d M Y') }}</span>
                                        @else
                                            {{ $ins->due_date->format('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($ins->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($ins->status == 'paid')
                                            <span class="badge bg-success rounded-pill px-3">Lunas</span>
                                        @elseif($ins->status == 'late')
                                            <span class="badge bg-danger rounded-pill px-3">Telat</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill px-3">Belum</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($ins->status != 'paid' && $loan->status != 'paid')
                                            <form action="{{ route('installments.pay', $ins->id) }}" method="POST" id="pay-form-{{ $ins->id }}">
                                                @csrf
                                                <button type="button" class="btn btn-pay-solid rounded-pill shadow-sm btn-pay-confirm"
                                                    data-form-id="pay-form-{{ $ins->id }}"
                                                    data-month="{{ $ins->installment_number }}"
                                                    data-amount="{{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}">
                                                    Bayar
                                                </button>
                                            </form>
                                        @elseif($ins->status == 'paid')
                                            <span class="text-success fw-bold"><i class="fas fa-check-circle"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Aset Jaminan</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="row g-4">
                    <div class="col-6">
                        <label class="small text-muted fw-bold mb-2">Foto Dokumen</label>
                        <div class="doc-img-box">
                            @if($loan->asset_document_path)
                                <img src="{{ asset('storage/' . $loan->asset_document_path) }}" alt="Dokumen" class="doc-img">
                                <a href="{{ asset('storage/' . $loan->asset_document_path) }}" target="_blank" class="doc-overlay-btn">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold mb-2">Foto Selfie</label>
                        <div class="doc-img-box">
                            @if($loan->asset_selfie_path)
                                <img src="{{ asset('storage/' . $loan->asset_selfie_path) }}" alt="Selfie" class="doc-img">
                                <a href="{{ asset('storage/' . $loan->asset_selfie_path) }}" target="_blank" class="doc-overlay-btn">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div class="lh-sm">
                                <span class="small text-muted d-block">Jenis Aset</span>
                                <span class="fw-bold text-dark">{{ $loan->asset_type ?? 'Aset Umum' }}</span>
                            </div>
                            <div class="text-end lh-sm">
                                <span class="small text-muted d-block">Nilai Estimasi</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($loan->asset_value, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- KOLOM KANAN: ANALISIS AI -->
    <div class="col-lg-4">
        <div class="card detail-card bg-white sticky-top" style="top: 80px; z-index: 1;">
            <div class="card-header bg-success bg-opacity-10 py-3 px-4 border-bottom-0">
                <h6 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Analisis Kredit AI</h6>
            </div>
            <div class="card-body p-4 text-center">

                <!-- Score Circle -->
                <div class="ai-score-container mb-3">
                    <svg width="100%" height="100%" viewBox="0 0 100 100" class="transform -rotate-90 w-100 h-100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#f1f3f5" stroke-width="8"/>
                        <!-- 2 * PI * 45 = 282.6 -->
                        <circle cx="50" cy="50" r="45" fill="none" stroke="{{ $loan->ai_score >= 75 ? '#3A6D48' : '#dc3545' }}" stroke-width="8"
                                stroke-dasharray="282.6"
                                stroke-dashoffset="{{ 282.6 - (282.6 * $loan->ai_score / 100) }}"
                                stroke-linecap="round" style="transition: stroke-dashoffset 1.5s ease-in-out;"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h2 class="fw-bold mb-0 text-dark" style="line-height: 1;">{{ $loan->ai_score }}</h2>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="badge {{ $loan->ai_score >= 75 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill">
                        {{ $loan->ai_score >= 75 ? 'Sangat Baik' : 'Berisiko' }}
                    </span>
                </div>

                <!-- Penjelasan AI -->
                <div class="alert alert-light border border-secondary border-opacity-10 rounded-3 mb-0 text-start p-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Kesimpulan Analisis</label>
                    <p class="mb-0 small text-dark lh-base text-justify">
                        @if($loan->ai_user_message)
                            {{ $loan->ai_user_message }}
                        @else
                            <em class="text-muted">Analisis detail tidak tersedia.</em>
                        @endif
                    </p>
                </div>

            </div>

            <!-- Tombol Pengajuan Ulang -->
            @if(in_array($loan->status, ['paid', 'rejected', 'canceled']))
                <div class="card-footer bg-white border-top p-3">
                    <a href="{{ route('loans.create') }}" class="btn btn-outline-finvera w-100 fw-bold rounded-pill">
                        <i class="fas fa-redo me-2"></i> Ajukan Lagi
                    </a>
                </div>
            @endif
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
        @if(isset($loan->loan) && $loan->status != 'pending')
            $('#installmentTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                searching: false,
                paging: true,
                pageLength: 5,
                lengthChange: false,
                info: false,
                ordering: false,
                scrollY: '300px',
                scrollCollapse: true
            });
        @endif

        $(document).on('click', '.btn-pay-confirm', function() {
            const formId = $(this).data('form-id');
            const month = $(this).data('month');
            const amount = $(this).data('amount');

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="mb-3">Anda akan membayar cicilan ke-<strong>${month}</strong></div>
                    <h2 class="text-success fw-bold">Rp ${amount}</h2>
                    <small class="text-muted">Pastikan saldo Anda mencukupi.</small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3A6D48',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Bayar Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        });

        @if(session('ai_analysis_popup'))
            const popupData = @json(session('ai_analysis_popup'));
            let icon = popupData.score >= 75 ? 'success' : 'warning';
            let confirmBtnColor = popupData.score >= 75 ? '#3A6D48' : '#f57c00';

            Swal.fire({
                title: popupData.title,
                html: `
                    <div class="text-center mb-3">
                        <h1 class="display-4 fw-bold" style="color: ${confirmBtnColor}">${popupData.score}</h1>
                        <span class="badge bg-light text-dark border">Skor Kredit AI</span>
                    </div>
                    <p class="text-justify px-2 small">${popupData.message}</p>
                `,
                icon: icon,
                confirmButtonText: 'Lihat Detail',
                confirmButtonColor: confirmBtnColor,
                width: 450,
                backdrop: `rgba(0,0,0,0.6)`
            });
        @endif
    });
</script>
@endpush
@endsection
