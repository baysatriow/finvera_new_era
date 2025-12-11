@extends('layouts.dashboard')

@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    /* --- Dashboard Styles --- */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        height: 100%;
        border: 1px solid #f0f0f0;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .score-badge {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .progress-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #f1f3f5;
        margin-top: 15px;
    }
    .progress-bar-custom {
        background-color: #3A6D48;
        border-radius: 4px;
    }

    /* Loan Summary */
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
    .summary-item:last-child {
        border-right: none;
    }
    .summary-label {
        color: #8898aa;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .summary-value {
        font-weight: 700;
        font-size: 1.25rem;
        color: #333;
    }
    .text-due-date {
        color: #dc3545;
    }

    /* Quick Actions */
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
    .action-btn-primary {
        background-color: #5d8f6e;
        color: white;
    }
    .action-btn-primary:hover {
        background-color: #4a7559;
        color: white;
        box-shadow: 0 5px 15px rgba(93, 143, 110, 0.3);
    }
    .action-btn-outline {
        background-color: white;
        border: 1px solid #e0e0e0;
        color: #333;
    }
    .action-btn-outline:hover {
        border-color: #5d8f6e;
        background-color: #f9fdfa;
        color: #2e5739;
    }
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .action-btn-primary .action-icon {
        background-color: rgba(255,255,255,0.2);
    }
    .action-btn-outline .action-icon {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    .action-btn:disabled, .action-btn.disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* AI Banner */
    .banner-ai {
        background: linear-gradient(135deg, #3A6D48 0%, #2c5236 100%);
        color: white;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    /* Loading Animation */
    .skeleton-text {
        display: inline-block;
        width: 100%;
        height: 1em;
        background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    @media (max-width: 768px) {
        .summary-item { border-right: none; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .summary-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    }
</style>

<!-- FEATURE 1: AI RECOMMENDATION BANNER -->
<div class="card banner-ai border-0 shadow-sm p-4">
    <div class="d-flex align-items-start gap-3 position-relative" style="z-index: 2;">
        <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-robot text-warning fs-4"></i>
        </div>
        <div style="flex: 1;">
            <h5 class="fw-bold mb-2">Rekomendasi Finansial AI</h5>
            <div id="ai-content">
                <div id="ai-loading" class="text-white-50 small">
                    <p class="mb-1"><i class="fas fa-circle-notch fa-spin me-2"></i>Sedang menganalisis profil...</p>
                    <span class="skeleton-text" style="width: 70%;"></span>
                </div>
                <p id="ai-result" class="mb-0 small text-white-50 d-none"></p>
            </div>
        </div>
    </div>
    <i class="fas fa-brain position-absolute" style="right: 20px; bottom: -20px; font-size: 6rem; opacity: 0.1;"></i>
</div>

<div class="row g-4 mb-4">
    @php
        $score = Auth::user()->credit_score ?? 0;
        $hasScore = $score > 0;
    @endphp

    @if($hasScore)
        <!-- FEATURE 2: CREDIT SCORE (Conditional) -->
        <div class="col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-bold mb-0">Credit Score</h6>
                    <i class="fas fa-chart-bar text-muted"></i>
                </div>

                <div class="d-flex align-items-center gap-3 mb-2">
                    @php
                        $scoreLabel = $score >= 700 ? 'Sangat Baik' : ($score >= 500 ? 'Cukup' : 'Perlu Perbaikan');
                        $scoreColor = $score >= 700 ? '#2e7d32' : ($score >= 500 ? '#f57c00' : '#c62828');
                        $progressWidth = ($score / 850) * 100;
                    @endphp
                    <h1 class="fw-bold mb-0" style="color: {{ $scoreColor }}">{{ $score }}</h1>
                    <span class="score-badge" style="color: {{ $scoreColor }}; background-color: {{ $scoreColor }}15;">
                        <i class="fas fa-arrow-up me-1"></i>{{ $scoreLabel }}
                    </span>
                </div>

                <div class="progress progress-custom">
                    <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $progressWidth }}%; background-color: {{ $scoreColor }};"></div>
                </div>
                <p class="text-muted small mt-2 mb-0">Diperbarui otomatis oleh AI setiap pengajuan.</p>
            </div>
        </div>
    @endif

    <!-- FEATURE 3: KYC STATUS -->
    <div class="{{ $hasScore ? 'col-md-6' : 'col-12' }}">
        <div class="stat-card">
            <h6 class="fw-bold mb-3">Status KYC</h6>

            @if(Auth::user()->kyc_status == 'verified')
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-check-circle text-success fs-4"></i>
                    <h5 class="fw-bold text-success mb-0">Terverifikasi</h5>
                </div>
                <p class="text-muted small mb-3">Akun siap untuk mengajukan pinjaman syariah.</p>
                <a href="{{ route('kyc.create') }}" class="text-decoration-none fw-bold text-finvera small">
                    Lihat Data <i class="fas fa-arrow-right ms-1"></i>
                </a>
            @elseif(Auth::user()->kyc_status == 'pending')
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-clock text-warning fs-4"></i>
                    <h5 class="fw-bold text-warning mb-0">Sedang Diproses</h5>
                </div>
                <p class="text-muted small mb-3">Mohon tunggu, tim kami sedang memverifikasi data Anda.</p>
                <a href="{{ route('kyc.create') }}" class="btn btn-sm btn-light text-muted">Lihat Detail</a>
            @else
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle text-danger fs-4"></i>
                    <h5 class="fw-bold text-danger mb-0">Belum Verifikasi</h5>
                </div>
                <p class="text-muted small mb-3">Lengkapi identitas untuk mulai meminjam.</p>
                <a href="{{ route('kyc.create') }}" class="btn btn-sm btn-finvera rounded-pill px-3">
                    Verifikasi Sekarang
                </a>
            @endif
        </div>
    </div>
</div>

<!-- FEATURE 4: LOAN SUMMARY -->
<div class="mb-4">
    <h6 class="fw-bold mb-3">Ringkasan Pinjaman Aktif</h6>
    <div class="loan-summary-card">
        @php
            $activeLoan = Auth::user()->loans()->where('status', 'active')->first();
            $pendingApp = Auth::user()->applications()->where('status', 'pending')->first();
        @endphp

        @if($activeLoan)
            <div class="row">
                <div class="col-md-4 summary-item">
                    <div class="summary-label">Total Pinjaman</div>
                    <div class="summary-value">Rp {{ number_format($activeLoan->total_amount, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-4 summary-item">
                    @php
                        $remainingInstallments = $activeLoan->installments()->where('status', 'pending')->count();
                    @endphp
                    <div class="summary-label">Sisa Cicilan</div>
                    <div class="summary-value text-success">{{ $remainingInstallments }} Bulan</div>
                </div>
                <div class="col-md-4 summary-item">
                    @php
                        $nextDue = $activeLoan->installments()
                            ->where('status', 'pending')
                            ->orderBy('due_date', 'asc')
                            ->first();
                    @endphp
                    <div class="summary-label">Jatuh Tempo Terdekat</div>
                    <div class="summary-value text-due-date">
                        {{ $nextDue ? $nextDue->due_date->format('d F Y') : '-' }}
                    </div>
                </div>
            </div>
        @elseif($pendingApp)
            <div class="text-center py-3">
                <div class="spinner-border text-warning mb-2" role="status"></div>
                <h6 class="fw-bold text-dark">Pengajuan Sedang Diproses</h6>
                <p class="text-muted mb-0 small">Nominal Pengajuan: Rp {{ number_format($pendingApp->amount, 0, ',', '.') }}</p>
            </div>
        @else
            <div class="text-center py-3">
                <img src="https://illustrations.popsy.co/gray/success.svg" alt="No Loan" style="height: 100px; opacity: 0.6;">
                <p class="text-muted mt-3 mb-0">Tidak ada pinjaman aktif saat ini.</p>
            </div>
        @endif
    </div>
</div>

<!-- FEATURE 5: QUICK ACTIONS -->
<div class="mb-4">
    <h6 class="fw-bold mb-3">Aksi Cepat</h6>
    <div class="row g-3">
        <div class="col-md-6">
            @if($activeLoan)
                <!-- Disabled: Ada Pinjaman Aktif -->
                <button class="action-btn action-btn-primary" onclick="Swal.fire('Tidak Dapat Mengajukan', 'Anda masih memiliki pinjaman aktif. Mohon lunasi terlebih dahulu.', 'warning')">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small style="opacity: 0.9;">Pinjaman Aktif Terdeteksi</small>
                    </div>
                </button>
            @elseif($pendingApp)
                <!-- Disabled: Ada Pengajuan Pending -->
                <button class="action-btn action-btn-primary" onclick="Swal.fire('Mohon Tunggu', 'Pengajuan Anda sebelumnya sedang dalam proses verifikasi.', 'info')">
                    <div class="action-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small style="opacity: 0.9;">Pengajuan Sedang Diproses</small>
                    </div>
                </button>
            @else
                <!-- Active: Ajukan Pinjaman -->
                <a href="{{ route('loans.create') }}" class="action-btn action-btn-primary">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                        <small style="opacity: 0.9;">Dapatkan Dana Hingga Rp 20 Juta</small>
                    </div>
                </a>
            @endif
        </div>

        <div class="col-md-6">
            @if($activeLoan)
                <!-- Active: Bayar Cicilan -->
                <a href="{{ route('loans.show', $activeLoan->application_id) }}" class="action-btn action-btn-outline">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Bayar Cicilan</h6>
                        <small class="text-muted">Bayar Cicilan Bulanan Anda</small>
                    </div>
                </a>
            @else
                <!-- Disabled: Tidak Ada Tagihan -->
                <button class="action-btn action-btn-outline" disabled style="opacity: 0.6; cursor: not-allowed;">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Bayar Cicilan</h6>
                        <small class="text-muted">Tidak ada tagihan aktif</small>
                    </div>
                </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Fetch AI Advice Async ---
        const loadingEl = document.getElementById('ai-loading');
        const resultEl = document.getElementById('ai-result');

        fetch("{{ route('dashboard.ai-advice') }}")
            .then(response => response.json())
            .then(data => {
                loadingEl.classList.add('d-none');
                resultEl.textContent = data.advice;
                resultEl.classList.remove('d-none');
                // Fade in effect
                resultEl.style.opacity = 0;
                resultEl.style.transition = 'opacity 0.5s';
                requestAnimationFrame(() => resultEl.style.opacity = 1);
            })
            .catch(error => console.error('Error fetching AI advice:', error));
    });
</script>
@endpush
@endsection
