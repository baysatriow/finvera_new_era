@extends('layouts.dashboard')

@section('page_title', 'Detail Pinjaman')

@section('content')
<!-- DataTables CSS -->
@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* --- Modern Card Styling --- */
    .detail-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: white;
        transition: transform 0.2s;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header-modern {
        background-color: #fff;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* --- Status Badge --- */
    .status-badge {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* --- Image Box --- */
    .doc-img-box {
        height: 220px;
        width: 100%;
        background-color: #f8f9fa;
        border: 2px dashed #e9ecef;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .doc-img {
        max-height: 90%;
        max-width: 90%;
        object-fit: contain;
        transition: transform 0.3s;
    }
    .doc-img-box:hover .doc-img { transform: scale(1.05); }
    .doc-overlay-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        color: #333;
        border: 1px solid #ddd;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .doc-img-box:hover .doc-overlay-btn { opacity: 1; }

    /* --- AI Score Circle --- */
    .ai-score-container {
        position: relative;
        margin: 0 auto;
        width: 130px;
        height: 130px;
    }
    .ai-score-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        line-height: 1;
    }

    /* --- Back Button --- */
    .btn-back-custom {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        color: #555;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .btn-back-custom:hover {
        background-color: #f8f9fa;
        color: #333;
        transform: translateX(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* --- AI Parsing Style --- */
    #ai-notes ul {
        padding-left: 20px;
        margin-top: 10px;
    }
    #ai-notes li {
        margin-bottom: 8px;
    }
    #ai-notes strong {
        color: #2c3e50;
    }

    /* --- Tombol Bayar --- */
    .btn-pay-solid {
        background-color: #3A6D48;
        color: white;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 6px 16px;
        border-radius: 50px;
        transition: all 0.2s;
    }
    .btn-pay-solid:hover {
        background-color: #2c5236;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(58, 109, 72, 0.2);
    }

    /* Custom Scrollbar untuk DataTables */
    .dataTables_scrollBody::-webkit-scrollbar {
        width: 6px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }
</style>

<div class="row g-4">
    <!-- Header & Tombol Kembali -->
    <div class="col-12 mb-2">
        <a href="{{ route('history') }}" class="btn-back-custom">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat
        </a>
    </div>

    <!-- KOLOM KIRI: INFO UTAMA -->
    <div class="col-lg-8">

        <!-- 1. Header Info & Status -->
        <div class="card detail-card">
            <div class="card-header-modern">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-file-invoice-dollar fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Nomor Pengajuan</small>
                        <h5 class="fw-bold text-dark mb-0 font-monospace">#{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</h5>
                    </div>
                </div>
                @php
                    $statusClass = match($loan->status) {
                        'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', 'paid' => 'primary', default => 'secondary'
                    };
                    $statusLabel = match($loan->status) {
                        'approved' => 'Aktif', 'pending' => 'Menunggu Review', 'rejected' => 'Ditolak', 'paid' => 'Lunas', default => ucfirst($loan->status)
                    };
                    $borderClass = $statusClass == 'warning' ? 'border-warning' : ($statusClass == 'danger' ? 'border-danger' : 'border-success');
                @endphp
                <span class="status-badge bg-{{ $statusClass }} {{ $statusClass == 'warning' ? 'text-dark' : 'text-white' }} border {{ $borderClass }}">
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-success h-100">
                            <small class="text-muted d-block mb-1 fw-bold text-uppercase">Nominal Pinjaman</small>
                            <h3 class="fw-bold text-finvera mb-0">Rp {{ number_format($loan->amount, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-4 h-100">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted small fw-bold">Tenor</span>
                                <span class="fw-bold text-dark">{{ $loan->tenor }} Bulan</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small fw-bold">Tanggal</span>
                                <span class="fw-bold text-dark">{{ $loan->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-4 border rounded-4 bg-white">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Tujuan Penggunaan</label>
                            <p class="mb-0 text-dark fst-italic fs-6">"{{ $loan->purpose }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Tabel Cicilan (DataTables Scroll) -->
        <div class="card detail-card">
            <div class="card-header-modern">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-calendar-alt me-2 text-finvera"></i>
                    {{ $loan->status == 'pending' ? 'Estimasi Jadwal' : 'Jadwal Pembayaran' }}
                </h6>
                @if(isset($loan->loan))
                    <span class="badge bg-light text-dark border font-monospace">{{ $loan->loan->loan_code }}</span>
                @endif
            </div>

            <div class="card-body px-4 pb-4 pt-0 mt-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100 table-sm" id="installmentTable">
                        <thead class="bg-light small text-muted text-uppercase">
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
                                        <td class="ps-3 fw-bold text-muted small">{{ $i }}</td>
                                        <td class="text-muted small">{{ $startDate->copy()->addMonths($i)->format('d M Y') }} <span class="badge bg-light text-muted ms-1">Est</span></td>
                                        <td class="fw-bold text-muted small">Rp {{ number_format($monthlyEst, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Draft</span>
                                        </td>
                                    </tr>
                                @endfor
                            @elseif($loan->loan)
                                <!-- REAL DATA -->
                                @foreach($loan->loan->installments as $ins)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark small">{{ $ins->installment_number }}</td>
                                    <td class="small">
                                        @if($ins->status != 'paid' && $ins->due_date < now())
                                            <span class="text-danger fw-bold">{{ $ins->due_date->format('d M Y') }}</span>
                                        @else
                                            {{ $ins->due_date->format('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark small">Rp {{ number_format($ins->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($ins->status == 'paid')
                                            <span class="badge bg-success rounded-pill px-2" style="font-size: 0.75rem;">Lunas</span>
                                        @elseif($ins->status == 'late')
                                            <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.75rem;">Telat</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-dark rounded-pill px-2" style="font-size: 0.75rem;">Belum</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($ins->status != 'paid' && $loan->status != 'paid')
                                            <form action="{{ route('installments.pay', $ins->id) }}" method="POST" id="pay-form-{{ $ins->id }}">
                                                @csrf
                                                <!-- Tombol Bayar Fixed -->
                                                <button type="button" class="btn btn-pay-solid btn-pay-confirm"
                                                    data-form-id="pay-form-{{ $ins->id }}"
                                                    data-month="{{ $ins->installment_number }}"
                                                    data-amount="{{ number_format($ins->amount + $ins->tazir_amount, 0, ',', '.') }}">
                                                    Bayar
                                                </button>
                                            </form>
                                        @elseif($ins->status == 'paid')
                                            <span class="text-success small fw-bold"><i class="fas fa-check-circle"></i></span>
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

        <!-- 3. Dokumen Aset -->
        <div class="card detail-card">
            <div class="card-header-modern">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-images me-2 text-warning"></i>Aset Jaminan</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0 mt-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 text-uppercase">Foto Dokumen</label>
                        <div class="doc-img-box">
                            @if($loan->asset_document_path)
                                <img src="{{ asset('storage/' . $loan->asset_document_path) }}" alt="Dokumen" class="doc-img">
                                <a href="{{ asset('storage/' . $loan->asset_document_path) }}" target="_blank" class="doc-overlay-btn" title="Lihat Penuh">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2 text-uppercase">Foto Selfie Aset</label>
                        <div class="doc-img-box">
                            @if($loan->asset_selfie_path)
                                <img src="{{ asset('storage/' . $loan->asset_selfie_path) }}" alt="Selfie" class="doc-img">
                                <a href="{{ asset('storage/' . $loan->asset_selfie_path) }}" target="_blank" class="doc-overlay-btn" title="Lihat Penuh">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border">
                            <div>
                                <span class="small text-muted d-block fw-bold">Jenis Aset</span>
                                <span class="text-dark">{{ $loan->asset_type ?? 'Aset Umum' }}</span>
                            </div>
                            <div class="text-end">
                                <span class="small text-muted d-block fw-bold">Nilai Estimasi</span>
                                <span class="fw-bold text-dark fs-5">Rp {{ number_format($loan->asset_value, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- KOLOM KANAN: ANALISIS AI -->
    <div class="col-lg-4">

        <!-- Analisis AI -->
        <div class="card detail-card sticky-top" style="top: 80px; z-index: 10;">
            <div class="card-header bg-success bg-opacity-10 py-3 px-4 border-bottom-0">
                <h6 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Analisis Kredit AI</h6>
            </div>
            <div class="card-body p-4 text-center">

                @php
                    $score = $loan->ai_score;
                    $scoreColor = $score >= 75 ? '#3A6D48' : ($score >= 50 ? '#ffc107' : '#dc3545');
                    $bgBadge = $score >= 75 ? 'bg-success' : ($score >= 50 ? 'bg-warning' : 'bg-danger');
                @endphp

                <!-- Score Circle -->
                <div class="ai-score-container mb-3 text-center">
                    <svg width="130" height="130" viewBox="0 0 130 130" class="transform -rotate-90 w-100 h-100">
                        <circle cx="65" cy="65" r="58" fill="none" stroke="#f1f3f5" stroke-width="8"/>
                        <circle cx="65" cy="65" r="58" fill="none" stroke="{{ $scoreColor }}" stroke-width="8"
                                stroke-dasharray="364.4"
                                stroke-dashoffset="{{ 364.4 - (364.4 * $score / 100) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="ai-score-value">{{ $score }}</div>
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">SKOR KREDIT</small>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <span class="badge {{ $bgBadge }} px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        {{ $score >= 75 ? 'Sangat Baik' : ($score >= 50 ? 'Cukup' : 'Berisiko') }}
                    </span>
                </div>

                <!-- Penjelasan AI -->
                <div class="alert alert-light border border-secondary border-opacity-25 rounded-3 mb-0 text-start p-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2 d-block border-bottom pb-2">Kesimpulan Analisis</label>
                    <div class="small text-dark lh-base text-justify ai-analysis-content" id="ai-notes">
                        <!-- Konten Mentah -->
                        {{ $loan->ai_user_message ?? 'Tidak ada analisis detail.' }}
                    </div>
                </div>

            </div>

            <!-- Tombol Pengajuan Ulang -->
            @if(in_array($loan->status, ['paid', 'rejected', 'canceled']))
                <div class="card-footer bg-white border-top p-3">
                    <a href="{{ route('loans.create') }}" class="btn btn-outline-success w-100 fw-bold rounded-pill">
                        <i class="fas fa-redo me-2"></i> Ajukan Pinjaman Lagi
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. FORMATTER AI TEXT ---
        const aiContainer = document.getElementById('ai-notes');
        if(aiContainer) {
            let text = aiContainer.innerHTML.trim();

            // Hapus prefix jika ada
            text = text.replace(/^Risk Analysis:\s*/i, '');

            // Format **Bold**
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // Format List Item
            text = text.replace(/(?:^|\n)-\s+(.*?)(?=\n|$)/g, '<div class="d-flex align-items-start mb-2"><i class="fas fa-circle text-success me-2 mt-2" style="font-size: 5px;"></i><span>$1</span></div>');

            // Format Baris Baru
            text = text.replace(/\n/g, '<br>');

            aiContainer.innerHTML = text;
        }

        // --- 2. DATATABLES ---
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

        // --- 3. SWAL PAYMENT CONFIRMATION ---
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
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    document.getElementById(formId).submit();
                }
            });
        });

        // --- 4. POPUP ANALISIS AI (ON REDIRECT) ---
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
                width: 500,
                padding: '2em',
                backdrop: `rgba(0,0,0,0.6)`
            });
        @endif
    });
</script>
@endpush
@endsection
