@extends('layouts.dashboard')

@section('page_title', 'Detail Pengajuan')

@section('content')
<style>
    .detail-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        background: white;
        transition: transform 0.2s;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .doc-img-box {
        height: 180px;
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
        bottom: 8px;
        right: 8px;
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
        text-decoration: none;
    }
    .doc-img-box:hover .doc-overlay-btn { opacity: 1; }
    .ai-score-container {
        position: relative;
        margin: 0 auto;
        width: 110px;
        height: 110px;
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <a href="{{ route('admin.applications') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="col-lg-8">

        <div class="card detail-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="fas fa-file-invoice-dollar fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold">ID Pengajuan</small>
                            <h5 class="fw-bold text-dark mb-0 font-monospace">#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</h5>
                        </div>
                    </div>
                    <span class="status-badge bg-warning text-dark">
                        <i class="fas fa-clock me-1"></i> Pending Review
                    </span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 border-start border-4 border-success h-100">
                            <small class="text-muted d-block mb-1 fw-bold">Nominal Diajukan</small>
                            <h4 class="fw-bold text-finvera mb-0">Rp {{ number_format($application->amount, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-light rounded-3 h-100">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tenor</span>
                                <span class="fw-bold text-dark">{{ $application->tenor }} Bulan</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Tanggal Masuk</span>
                                <span class="fw-bold text-dark">{{ $application->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-white">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Tujuan Penggunaan</label>
                            <p class="mb-0 text-dark fst-italic">"{{ $application->purpose }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Profil Peminjam</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-muted" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                {{ substr($application->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $application->user->name }}</h6>
                                <span class="text-muted small">{{ $application->user->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                         <a href="{{ route('admin.borrowers.show', $application->user_id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                            Lihat Profil Lengkap <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                    <div class="col-12"><hr class="my-0 border-light"></div>

                    <div class="col-md-6 mt-3">
                        <small class="text-muted d-block fw-bold mb-1">Pekerjaan</small>
                        <span>{{ $application->user->job }}</span>
                    </div>
                    <div class="col-md-6 mt-3">
                        <small class="text-muted d-block fw-bold mb-1">Penghasilan Bulanan</small>
                        <span class="fw-bold text-success">Rp {{ number_format($application->user->monthly_income, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted d-block fw-bold mb-1">Alamat Domisili</small>
                        <span class="text-dark small">{{ $application->user->address_full }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark">Dokumen Aset Jaminan</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted mb-2 fw-bold">Foto Dokumen</label>
                        <div class="doc-img-box">
                            @if($application->asset_document_path)
                                <img src="{{ asset('storage/' . $application->asset_document_path) }}" class="doc-img" alt="Dokumen">
                                <a href="{{ asset('storage/' . $application->asset_document_path) }}" target="_blank" class="doc-overlay-btn" title="Lihat Penuh">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">Tidak ada gambar</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-2 fw-bold">Foto Selfie Aset</label>
                        <div class="doc-img-box">
                            @if($application->asset_selfie_path)
                                <img src="{{ asset('storage/' . $application->asset_selfie_path) }}" class="doc-img" alt="Selfie">
                                <a href="{{ asset('storage/' . $application->asset_selfie_path) }}" target="_blank" class="doc-overlay-btn" title="Lihat Penuh">
                                    <i class="fas fa-expand-alt"></i>
                                </a>
                            @else
                                <span class="text-muted small">Tidak ada gambar</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <span class="small text-muted d-block fw-bold">Jenis Aset</span>
                                <span class="text-dark small text-uppercase">{{ Str::limit($application->asset_type ?? 'Aset Umum', 30) }}</span>
                            </div>
                            <div class="text-end">
                                <span class="small text-muted d-block fw-bold">Estimasi Nilai</span>
                                <span class="fw-bold text-dark fs-5">Rp {{ number_format($application->asset_value, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4 bg-light border-0">
            <div class="card-body p-4">
                <div class="d-grid gap-3 d-md-flex">
                    <button type="button" class="btn btn-outline-danger fw-bold btn-reject-action flex-fill py-3">
                        <i class="fas fa-times-circle me-2"></i> Tolak Pengajuan
                    </button>
                    <button type="button" class="btn btn-success btn-lg fw-bold shadow-sm btn-approve-action flex-fill py-3">
                        <i class="fas fa-check-circle me-2"></i> Setujui Pengajuan
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="card detail-card bg-white mb-4 sticky-top" style="top: 80px; z-index: 1;">
            <div class="card-header bg-success bg-opacity-10 py-3 px-4 border-bottom-0">
                <h6 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Analisis Risiko AI</h6>
            </div>
            <div class="card-body p-4">

                <div class="ai-score-container mb-3 text-center">
                    <svg width="110" height="110" viewBox="0 0 100 100" class="transform -rotate-90 w-100 h-100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#f1f3f5" stroke-width="8"/>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="{{ $application->ai_score >= 75 ? '#3A6D48' : '#dc3545' }}" stroke-width="8"
                                stroke-dasharray="282.6"
                                stroke-dashoffset="{{ 282.6 - (282.6 * $application->ai_score / 100) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h2 class="fw-bold mb-0 text-dark" style="line-height: 1;">{{ $application->ai_score }}</h2>
                        <span class="text-muted fw-bold" style="font-size: 0.6rem;">SKOR AI</span>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <span class="badge {{ $application->ai_score >= 75 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill">
                        {{ $application->ai_score >= 75 ? 'Sangat Baik' : 'Berisiko' }}
                    </span>
                </div>

                <div class="alert alert-light border border-secondary border-opacity-10 rounded-3 mb-0 text-start p-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Catatan Analisis</label>
                    <div class="small text-dark lh-base text-justify" id="ai-notes">
                        {{ $application->admin_note ?? 'Tidak ada catatan khusus.' }}
                    </div>
                </div>

                <form id="approve-form" action="{{ route('admin.approve', $application->id) }}" method="POST" class="d-none">@csrf</form>
                <form id="reject-form" action="{{ route('admin.reject', $application->id) }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="reason" id="reject-reason">
                </form>

            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const aiNotes = document.getElementById('ai-notes');
        if (aiNotes) {
            let text = aiNotes.innerHTML;
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\* /g, '<br>&bull; ');
            aiNotes.innerHTML = text;
        }

        document.querySelector('.btn-approve-action').addEventListener('click', function() {
            Swal.fire({
                title: 'Setujui Pinjaman?',
                html: "Anda akan menyetujui pengajuan ini.<br>Dana akan segera dicairkan dan status menjadi Aktif.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3A6D48',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui & Cairkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({title: 'Memproses...', didOpen: () => Swal.showLoading()});
                    document.getElementById('approve-form').submit();
                }
            });
        });

        document.querySelector('.btn-reject-action').addEventListener('click', function() {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Berikan alasan penolakan untuk pengguna:",
                input: 'textarea',
                inputPlaceholder: 'Contoh: Dokumen aset buram, rasio gaji tidak cukup...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tolak Pengajuan',
                inputValidator: (value) => {
                    if (!value) return 'Anda wajib mengisi alasan penolakan!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-reason').value = result.value;
                    Swal.fire({title: 'Memproses...', didOpen: () => Swal.showLoading()});
                    document.getElementById('reject-form').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
