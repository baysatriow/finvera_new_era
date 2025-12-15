@extends('layouts.dashboard')

@section('page_title', 'Review Pengajuan')

@section('content')
<style>
    /* Styling khusus halaman detail */
    .card-section {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        background: #fff;
        overflow: hidden;
    }

    .card-header-custom {
        background-color: #fff;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f3f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .badge-status {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    /* Tombol Kembali yang Jelas */
    .btn-back-custom {
        color: #333;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 50px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .btn-back-custom:hover {
        background-color: #f8f9fa;
        color: #3A6D48;
        border-color: #3A6D48;
        transform: translateX(-3px);
    }

    /* Styling AI Analysis agar rapi */
    .ai-analysis-content {
        font-size: 0.95rem;
        line-height: 1.7;
        color: #495057;
    }
    .ai-analysis-content strong {
        color: #2c3e50;
        font-weight: 700;
    }
    .ai-analysis-content ul {
        padding-left: 20px;
        margin-top: 10px;
        margin-bottom: 15px;
    }
    .ai-analysis-content li {
        margin-bottom: 6px;
    }

    /* Foto Aset */
    .doc-preview-container {
        position: relative;
        height: 240px;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .doc-preview {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .doc-preview-container:hover .doc-preview {
        transform: scale(1.05);
    }
    .btn-zoom {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255,255,255,0.9);
        border: 1px solid #dee2e6;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        opacity: 0;
        transition: opacity 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .doc-preview-container:hover .btn-zoom {
        opacity: 1;
    }

    /* Skor Kredit */
    .score-circle-lg {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 8px solid;
        margin: 0 auto;
        position: relative;
    }
    .score-circle-lg.score-high { border-color: #3A6D48; color: #3A6D48; }
    .score-circle-lg.score-med { border-color: #ffc107; color: #ffc107; }
    .score-circle-lg.score-low { border-color: #dc3545; color: #dc3545; }

    .score-value { font-size: 2.5rem; font-weight: 800; line-height: 1; }
    .score-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6c757d; margin-top: 5px; }
</style>

<div class="row">
    <!-- Header & Tombol Kembali -->
    <div class="col-12 mb-4 d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.applications') }}" class="btn-back-custom">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- KOLOM KIRI: Data & Aset -->
    <div class="col-lg-8">

        <!-- Rincian Pinjaman -->
        <div class="card card-section">
            <div class="card-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="fas fa-file-invoice-dollar fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Rincian Pengajuan</h6>
                        <small class="text-muted">ID: #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                </div>
                <span class="badge bg-warning text-dark border border-warning badge-status">
                    <i class="fas fa-clock me-1"></i> Menunggu Review
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-success h-100">
                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Nominal Pengajuan</small>
                            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($application->amount, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small fw-bold">Tenor</span>
                                <span class="fw-bold text-dark">{{ $application->tenor }} Bulan</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small fw-bold">Tanggal Masuk</span>
                                <span class="fw-bold text-dark">{{ $application->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-white">
                            <label class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Tujuan Penggunaan</label>
                            <p class="mb-0 text-dark fst-italic">"{{ $application->purpose }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Peminjam -->
        <div class="card card-section">
            <div class="card-header-custom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-circle me-2 text-primary"></i>Profil Peminjam</h6>
                <a href="{{ route('admin.borrowers.show', $application->user_id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                    Lihat Profil Lengkap <i class="fas fa-external-link-alt ms-1"></i>
                </a>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold mb-1">Nama Lengkap</label>
                            <div class="fw-bold text-dark">{{ $application->user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold mb-1">NIK (Verified)</label>
                            <div class="font-monospace text-dark bg-light px-2 py-1 rounded d-inline-block">{{ $application->user->kyc->nik ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="small text-muted fw-bold mb-1">Alamat Domisili</label>
                            <div class="text-dark small">{{ $application->user->address_full }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 border-start border-light ps-md-4">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold mb-1">Pekerjaan</label>
                            <div class="text-dark">{{ $application->user->job }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold mb-1">Penghasilan Bulanan</label>
                            <div class="fw-bold text-success fs-5">Rp {{ number_format($application->user->monthly_income, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <label class="small text-muted fw-bold mb-1">Status Kepegawaian</label>
                            <div class="text-dark">{{ $application->user->employment_duration }} Bulan Kerja</div>
                        </div>
                    </div>

                    <!-- INFO REKENING PENCAIRAN (BARU) -->
                    <div class="col-12 mt-2">
                        <div class="p-3 bg-light rounded-3 border-top border-success border-2">
                            <h6 class="fw-bold text-dark mb-3 small text-uppercase"><i class="fas fa-university me-2 text-success"></i>Rekening Pencairan Utama</h6>
                            @php
                                $primaryBank = $application->user->bankAccounts->where('is_primary', true)->first();
                            @endphp

                            @if($primaryBank)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $primaryBank->bank_name }}</div>
                                        <div class="small text-muted">{{ $primaryBank->account_holder_name }}</div>
                                    </div>
                                    <div class="font-monospace fw-bold fs-5 text-dark">{{ $primaryBank->account_number }}</div>
                                </div>
                            @else
                                <div class="text-danger small fst-italic"><i class="fas fa-exclamation-circle me-1"></i> Belum ada rekening utama terdaftar.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Aset -->
        <div class="card card-section">
            <div class="card-header-custom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-images me-2 text-warning"></i>Dokumen Jaminan</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2">Foto Dokumen Asli</label>
                        <div class="doc-preview-container">
                            <img src="{{ asset('storage/' . $application->asset_document_path) }}" class="doc-preview" alt="Dokumen">
                            <a href="{{ asset('storage/' . $application->asset_document_path) }}" target="_blank" class="btn-zoom" title="Perbesar">
                                <i class="fas fa-expand-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-2">Foto Selfie Aset</label>
                        <div class="doc-preview-container">
                            <img src="{{ asset('storage/' . $application->asset_selfie_path) }}" class="doc-preview" alt="Selfie">
                            <a href="{{ asset('storage/' . $application->asset_selfie_path) }}" target="_blank" class="btn-zoom" title="Perbesar">
                                <i class="fas fa-expand-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                            <div>
                                <small class="text-muted d-block fw-bold">Jenis Aset</small>
                                <span class="text-dark">{{ $application->asset_type ?? 'Aset Umum' }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block fw-bold">Estimasi Nilai</small>
                                <span class="fw-bold text-dark fs-5">Rp {{ number_format($application->asset_value, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMBOL AKSI DISINI -->
        <div class="card card-section border-0 shadow-sm bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark text-uppercase small ls-1">Keputusan Akhir</h6>
                <div class="d-flex flex-column flex-md-row gap-3">
                    <button type="button" class="btn btn-outline-danger fw-bold btn-reject py-3 px-4 flex-fill rounded-3">
                        <i class="fas fa-times-circle me-2"></i> TOLAK PENGAJUAN
                    </button>
                    <button type="button" class="btn btn-success btn-lg fw-bold shadow-sm btn-approve py-3 px-4 flex-fill rounded-3">
                        <i class="fas fa-check-circle me-2"></i> SETUJUI PENGAJUAN
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Forms -->
        <form id="approve-form" action="{{ route('admin.approve', $application->id) }}" method="POST" class="d-none">@csrf</form>
        <form id="reject-form" action="{{ route('admin.reject', $application->id) }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="reason" id="reject-reason">
        </form>

    </div>

    <!-- KOLOM KANAN: ANALISIS AI -->
    <div class="col-lg-4">

        <!-- Analisis AI -->
        <div class="card card-section border-top-4 border-success sticky-top" style="top: 80px; z-index: 10;">
            <div class="card-header bg-success bg-opacity-10 py-3 px-4 border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Analisis Risiko AI</h6>
                    <span class="badge bg-white text-success border border-success">{{ $application->ai_score }} / 100</span>
                </div>
            </div>
            <div class="card-body p-4">

                @php
                    $score = $application->ai_score;
                    $scoreClass = $score >= 75 ? 'score-high' : ($score >= 50 ? 'score-med' : 'score-low');
                    $scoreText = $score >= 75 ? 'Sangat Baik' : ($score >= 50 ? 'Cukup' : 'Berisiko');
                    $bgClass = $score >= 75 ? 'bg-success' : ($score >= 50 ? 'bg-warning' : 'bg-danger');
                @endphp

                <div class="score-circle-lg {{ $scoreClass }} mb-4">
                    <div class="score-value">{{ $score }}</div>
                    <div class="score-label">SKOR KREDIT</div>
                </div>

                <div class="text-center mb-4">
                    <span class="badge {{ $bgClass }} text-white px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        {{ $scoreText }}
                    </span>
                </div>

                <div class="alert alert-light border border-secondary border-opacity-25 rounded-3 mb-0">
                    <!-- Kontainer Hasil Analisis yang akan di-format JS -->
                    <div class="ai-analysis-content text-justify" id="ai-content">
                        {{ $application->admin_note }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. FORMATTER AI TEXT (Advanced Markdown Parsing) ---
        const aiContainer = document.getElementById('ai-content');
        if(aiContainer) {
            let text = aiContainer.innerHTML;

            // Hapus prefix "Risk Analysis:" jika ada
            text = text.replace(/^Risk Analysis:\s*/i, '');

            // Format **Bold** menjadi <strong>
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="text-dark">$1</strong>');

            // Format List Item (- Item)
            // Regex mencari pola "- Teks" dan membungkusnya dalam div list item
            text = text.replace(/- (.*?)(?=\n|$)/g, '<div class="d-flex align-items-start mb-2"><i class="fas fa-circle text-success me-2 mt-2" style="font-size: 5px;"></i><span>$1</span></div>');

            // Format Baris Baru (selain yang sudah jadi list)
            text = text.replace(/\n/g, '<br>');

            aiContainer.innerHTML = text;
        }

        // --- 2. LOGIC SWEETALERT ---

        // Approve
        document.querySelector('.btn-approve').addEventListener('click', function() {
            Swal.fire({
                title: 'Setujui & Cairkan?',
                html: "Dana sebesar <strong class='text-success fs-5'>Rp {{ number_format($application->amount, 0, ',', '.') }}</strong> akan segera dicairkan ke rekening peminjam.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3A6D48',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Proses Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({title: 'Memproses...', text: 'Mohon tunggu sebentar', didOpen: () => Swal.showLoading()});
                    document.getElementById('approve-form').submit();
                }
            });
        });

        // Reject
        document.querySelector('.btn-reject').addEventListener('click', function() {
            Swal.fire({
                title: 'Tolak Pengajuan',
                text: "Silakan masukkan alasan penolakan yang jelas:",
                input: 'textarea',
                inputPlaceholder: 'Contoh: Rasio gaji tidak mencukupi untuk angsuran...',
                inputAttributes: {
                    'aria-label': 'Alasan penolakan'
                },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak Pengajuan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) return 'Alasan wajib diisi agar pengguna mengerti!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason').value = result.value;
                    Swal.fire({title: 'Menolak...', text: 'Sedang mengirim keputusan', didOpen: () => Swal.showLoading()});
                    document.getElementById('reject-form').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
