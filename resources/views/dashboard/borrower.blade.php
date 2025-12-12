@extends('layouts.dashboard')

@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    /* --- Dashboard Styling --- */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        height: 100%;
        border: 1px solid #f0f0f0;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-2px); }

    .score-badge {
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    /* Progress Bar */
    .progress-custom {
        height: 10px;
        border-radius: 5px;
        background-color: #e9ecef;
        margin-top: 15px;
        overflow: hidden;
    }
    .progress-bar-custom {
        border-radius: 5px;
        transition: width 1.5s ease-in-out;
    }

    /* Summary Card */
    .loan-summary-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f0f0f0;
    }
    .summary-item {
        text-align: center;
        padding: 10px;
        border-right: 1px solid #eee;
    }
    .summary-item:last-child { border-right: none; }
    .summary-label {
        color: #8898aa;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .summary-value {
        font-weight: 800;
        font-size: 1.35rem;
        color: #333;
    }

    /* Action Buttons */
    .action-btn {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid transparent;
        width: 100%;
        text-align: left;
    }
    .action-btn-primary { background-color: #3A6D48; color: white; }
    .action-btn-primary:hover { background-color: #2c5236; color: white; box-shadow: 0 8px 20px rgba(58, 109, 72, 0.25); }

    .action-btn-outline { background-color: white; border: 1px solid #e0e0e0; color: #333; }
    .action-btn-outline:hover { border-color: #3A6D48; background-color: #f4fcf6; color: #3A6D48; }

    .action-btn-disabled {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
        cursor: pointer;
    }
    .action-btn-disabled:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
    }

    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-right: 16px;
        flex-shrink: 0;
    }
    .action-btn-primary .action-icon { background-color: rgba(255,255,255,0.2); }
    .action-btn-outline .action-icon { background-color: #e8f5e9; color: #3A6D48; }
    .action-btn-disabled .action-icon { background-color: #e9ecef; color: #adb5bd; }

    /* AI Banner */
    .banner-ai {
        background: linear-gradient(135deg, #3A6D48 0%, #244230 100%);
        color: white;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        min-height: 140px;
    }

    /* Skeleton Animation */
    .skeleton-text {
        display: inline-block;
        height: 0.9em;
        background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.25) 50%, rgba(255,255,255,0.1) 75%);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
        border-radius: 4px;
        margin-bottom: 6px;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    @media (max-width: 768px) {
        .summary-item { border-right: none; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .summary-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    }
</style>

<div class="card banner-ai border-0 shadow-sm p-4">
    <div class="d-flex align-items-start gap-3 position-relative" style="z-index: 2;">
        <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
            <i class="fas fa-robot text-warning fs-3"></i>
        </div>
        <div style="flex: 1;">
            <h6 class="fw-bold mb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem; opacity: 0.9;">FinVera AI Advisor</h6>
            <div id="ai-content">
                <div id="ai-loading" class="text-white-50">
                    <p class="mb-2 small"><i class="fas fa-circle-notch fa-spin me-2"></i>Menganalisis profil finansial Anda...</p>
                    <span class="skeleton-text" style="width: 80%;"></span><br>
                    <span class="skeleton-text" style="width: 60%;"></span>
                </div>
                <p id="ai-result" class="mb-0 small text-white lh-base d-none" style="font-size: 0.95rem;"></p>
            </div>
        </div>
    </div>
    <i class="fas fa-brain position-absolute" style="right: -20px; bottom: -40px; font-size: 10rem; opacity: 0.05;"></i>
</div>

<div class="row g-4 mb-4">
    @php
        $score = Auth::user()->credit_score ?? 0;
        $hasScore = $score > 0;
    @endphp

    @if($hasScore)
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Skor Kredit</h6>
                        <small class="text-muted">Analisis Risiko AI</small>
                    </div>
                    <div class="bg-light rounded-circle p-2 text-muted">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>

                <div class="d-flex align-items-end gap-2 mb-2">
                    @php
                        $scoreLabel = $score >= 80 ? 'Sangat Baik' : ($score >= 50 ? 'Cukup' : 'Berisiko');
                        $scoreColor = $score >= 80 ? '#2e7d32' : ($score >= 50 ? '#f57c00' : '#c62828');
                        $bgBadge = $score >= 80 ? '#e8f5e9' : ($score >= 50 ? '#fff3e0' : '#ffebee');
                    @endphp
                    <h1 class="fw-bold mb-0 display-5" style="color: {{ $scoreColor }}; line-height: 1;">{{ $score }}</h1>
                    <span class="score-badge mb-1" style="color: {{ $scoreColor }}; background-color: {{ $bgBadge }};">
                        {{ $scoreLabel }}
                    </span>
                </div>

                <div class="progress progress-custom">
                    <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $score }}%; background-color: {{ $scoreColor }};"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="{{ $hasScore ? 'col-md-6' : 'col-12' }}">
        <div class="stat-card {{ !$hasScore ? 'text-center py-5' : '' }}">
            <div class="d-flex {{ !$hasScore ? 'justify-content-center' : 'justify-content-between' }} align-items-start mb-3">
                @if(!$hasScore)
                    <div class="text-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 mb-3 d-inline-flex mx-auto">
                            <i class="fas fa-id-card fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Status Identitas</h5>
                        <small class="text-muted">Know Your Customer (KYC)</small>
                    </div>
                @else
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Status Identitas</h6>
                        <small class="text-muted">Know Your Customer (KYC)</small>
                    </div>
                    <div class="bg-light rounded-circle p-2 text-muted">
                        <i class="fas fa-id-card"></i>
                    </div>
                @endif
            </div>

            <div class="{{ !$hasScore ? 'mt-3' : 'mt-auto' }}">
                @if(Auth::user()->kyc_status == 'verified')
                    <div class="d-flex {{ !$hasScore ? 'justify-content-center' : '' }} align-items-center gap-3 mb-3">
                        <i class="fas fa-check-circle text-success fs-2"></i>
                        <div class="{{ !$hasScore ? 'text-start' : '' }}">
                            <h5 class="fw-bold text-success mb-0">Terverifikasi</h5>
                            <small class="text-muted">Data valid & aman</small>
                        </div>
                    </div>
                    <a href="{{ route('kyc.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Data</a>
                @elseif(Auth::user()->kyc_status == 'pending')
                    <div class="d-flex {{ !$hasScore ? 'justify-content-center' : '' }} align-items-center gap-3 mb-3">
                        <div class="spinner-grow text-warning" style="width: 2rem; height: 2rem;" role="status"></div>
                        <div class="{{ !$hasScore ? 'text-start' : '' }}">
                            <h5 class="fw-bold text-warning mb-0">Diproses</h5>
                            <small class="text-muted">Sedang ditinjau admin</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light rounded-pill px-3" disabled>Menunggu...</button>
                @else
                    <div class="d-flex {{ !$hasScore ? 'justify-content-center' : '' }} align-items-center gap-3 mb-3">
                        @if($hasScore)
                            <i class="fas fa-exclamation-circle text-danger fs-2"></i>
                        @endif
                        <div class="{{ !$hasScore ? 'text-center' : '' }}">
                            @if(!$hasScore)
                                <h4 class="fw-bold text-danger mb-1">Belum Verifikasi</h4>
                                <p class="text-muted mb-0">Wajib melakukan verifikasi KTP & Selfie untuk pinjaman.</p>
                            @else
                                <h5 class="fw-bold text-danger mb-0">Belum Verifikasi</h5>
                                <small class="text-muted">Wajib untuk pinjaman</small>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('kyc.create') }}" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm">
                        Verifikasi Sekarang
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="fw-bold mb-3 text-dark">Ringkasan Pinjaman Aktif</h6>
    <div class="loan-summary-card">
        @php
            $activeLoan = Auth::user()->loans()->where('status', 'active')->first();
            $pendingApp = Auth::user()->applications()->where('status', 'pending')->first();
        @endphp

        @if($activeLoan)
            <div class="row">
                <div class="col-md-4 summary-item">
                    <div class="summary-label">Sisa Pokok</div>
                    <div class="summary-value text-primary">Rp {{ number_format($activeLoan->remaining_balance, 0, ',', '.') }}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">Dari total Rp {{ number_format($activeLoan->total_amount, 0, ',', '.') }}</small>
                </div>
                <div class="col-md-4 summary-item">
                    @php
                        $remainingInstallments = $activeLoan->installments()->where('status', 'pending')->count();
                    @endphp
                    <div class="summary-label">Sisa Tenor</div>
                    <div class="summary-value text-dark">{{ $remainingInstallments }} <span class="fs-6 text-muted fw-normal">Bulan</span></div>
                </div>
                <div class="col-md-4 summary-item">
                    @php
                        $nextDue = $activeLoan->installments()
                            ->where('status', 'pending')
                            ->orderBy('due_date', 'asc')
                            ->first();
                    @endphp
                    <div class="summary-label">Jatuh Tempo</div>
                    <div class="summary-value text-danger">
                        {{ $nextDue ? $nextDue->due_date->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>
        @elseif($pendingApp)
            <div class="text-center py-4">
                <div class="spinner-border text-warning mb-2" role="status"></div>
                <h6 class="fw-bold text-dark mt-2">Pengajuan Sedang Diproses</h6>
                <p class="text-muted mb-0 small">Nominal: Rp {{ number_format($pendingApp->amount, 0, ',', '.') }}</p>
            </div>
        @else
            <div class="text-center py-4">
                <div class="bg-light rounded-circle d-inline-flex p-3 mb-2">
                    <i class="fas fa-check text-muted opacity-50 fs-3"></i>
                </div>
                <p class="text-muted mb-0 fw-bold">Tidak ada tagihan aktif saat ini.</p>
                <small class="text-muted">Anda bebas dari tanggungan.</small>
            </div>
        @endif
    </div>
</div>

<div class="mb-4">
    <h6 class="fw-bold mb-3 text-dark">Aksi Cepat</h6>
    <div class="row g-3">
        <div class="col-md-6">
            @if($activeLoan)
                <button class="action-btn action-btn-disabled" onclick="Swal.fire('Info', 'Anda memiliki pinjaman aktif. Mohon lunasi terlebih dahulu.', 'info')">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small>Fitur dikunci sementara</small>
                    </div>
                </button>
            @elseif($pendingApp)
                <button class="action-btn action-btn-disabled" onclick="Swal.fire('Info', 'Pengajuan Anda sedang diproses oleh tim kami.', 'info')">
                    <div class="action-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small>Menunggu persetujuan</small>
                    </div>
                </button>
            @else
                <a href="{{ route('loans.create') }}" class="action-btn action-btn-primary">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small style="opacity: 0.9;">Dana cair hingga Rp 20 Juta</small>
                    </div>
                </a>
            @endif
        </div>

        <div class="col-md-6">
            @if($activeLoan)
                <a href="{{ route('loans.show', $activeLoan->application_id) }}" class="action-btn action-btn-outline">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Bayar Cicilan</h6>
                        <small class="text-muted">Lihat tagihan & pembayaran</small>
                    </div>
                </a>
            @else
                <button class="action-btn action-btn-disabled" disabled style="cursor: not-allowed; opacity: 0.5;">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Bayar Cicilan</h6>
                        <small>Tidak ada tagihan</small>
                    </div>
                </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingEl = document.getElementById('ai-loading');
        const resultEl = document.getElementById('ai-result');

        // Helper format Markdown sederhana
        function formatAiResponse(text) {
            if (!text) return '';
            let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            formatted = formatted.replace(/\n/g, '<br>');
            return formatted;
        }

        // Fetch Advice AI
        fetch("{{ route('dashboard.ai-advice') }}")
            .then(response => response.json())
            .then(data => {
                loadingEl.classList.add('d-none');
                resultEl.innerHTML = formatAiResponse(data.advice);
                resultEl.classList.remove('d-none');

                resultEl.style.opacity = 0;
                resultEl.style.transition = 'opacity 0.8s';
                requestAnimationFrame(() => resultEl.style.opacity = 1);
            })
            .catch(error => {
                console.warn('AI fetch background error:', error);
            });
    });
</script>
@endpush
@endsection
