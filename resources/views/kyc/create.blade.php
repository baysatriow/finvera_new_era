@extends('layouts.dashboard')

@section('page_title', 'Identitas & Verifikasi (KYC)')

@section('content')
<style>
    /* Upload Container */
    .upload-container {
        position: relative;
        width: 100%;
        height: 280px;
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 16px;
        transition: all 0.3s ease;
        overflow: hidden;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .upload-container:hover {
        border-color: #3A6D48;
        background-color: #f4fcf6;
    }
    .upload-container.has-image {
        border-style: solid;
        border-color: #3A6D48;
        background-color: #212529;
    }

    /* Elements inside Upload */
    .upload-placeholder {
        text-align: center;
        color: #adb5bd;
        transition: opacity 0.2s;
        width: 100%;
        padding: 20px;
    }
    .upload-icon {
        font-size: 3.5rem;
        margin-bottom: 15px;
        color: #ced4da;
    }
    .upload-container:hover .upload-icon { color: #3A6D48; }

    .img-preview {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 5;
    }

    .btn-remove-img {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.9);
        color: #dc3545;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 10;
        transition: transform 0.2s;
    }
    .btn-remove-img:hover {
        transform: scale(1.1);
        background: white;
    }

    /* Submit Button */
    .btn-submit-kyc {
        background-color: #3A6D48;
        color: white;
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border: none;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(58, 109, 72, 0.2);
    }
    .btn-submit-kyc:hover {
        background-color: #2c5236;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(58, 109, 72, 0.3);
    }

    /* Read Only View */
    .verified-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }
    .doc-display {
        height: 240px;
        width: 100%;
        object-fit: contain;
        background-color: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #eee;
    }
</style>

<div class="row">
    <div class="col-12">
        @if(in_array($user->kyc_status, ['verified', 'pending']))
            <div class="verified-card mb-4">
                <div class="p-4 {{ $user->kyc_status == 'verified' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas {{ $user->kyc_status == 'verified' ? 'fa-check-circle' : 'fa-clock' }} fs-2"></i>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{ $user->kyc_status == 'verified' ? 'Akun Terverifikasi' : 'Verifikasi Diproses' }}
                            </h5>
                            <small class="opacity-75">
                                {{ $user->kyc_status == 'verified' ? 'Data identitas Anda valid.' : 'Tim kami sedang meninjau dokumen Anda.' }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Informasi Identitas</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                <i class="fas fa-id-card text-muted me-3 fs-4"></i>
                                <div>
                                    <small class="d-block text-muted">Nomor Induk Kependudukan (NIK)</small>
                                    <span class="fw-bold fs-5 text-dark font-monospace">{{ $user->kyc->nik ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Foto KTP</label>
                            @if($user->kyc && $user->kyc->ktp_image_path)
                                <img src="{{ asset('storage/' . $user->kyc->ktp_image_path) }}" class="doc-display shadow-sm" alt="Foto KTP">
                            @else
                                <div class="doc-display d-flex align-items-center justify-content-center text-muted">Tidak ada gambar</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold text-uppercase mb-2">Foto Selfie</label>
                            @if($user->kyc && $user->kyc->selfie_image_path)
                                <img src="{{ asset('storage/' . $user->kyc->selfie_image_path) }}" class="doc-display shadow-sm" alt="Foto Selfie">
                            @else
                                <div class="doc-display d-flex align-items-center justify-content-center text-muted">Tidak ada gambar</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-4 px-4 border-bottom-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="fas fa-user-shield fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Verifikasi Identitas</h5>
                            <p class="text-muted small mb-0">Lengkapi data diri untuk membuka akses pinjaman.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if($user->kyc_status == 'rejected')
                        <div class="alert alert-danger border-0 d-flex align-items-center mb-4 rounded-3 shadow-sm p-3">
                            <i class="fas fa-exclamation-circle fs-3 me-3"></i>
                            <div>
                                <strong>Verifikasi Sebelumnya Ditolak</strong><br>
                                <span class="small">{{ $user->kyc->rejection_reason ?? 'Foto tidak jelas.' }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data" id="kycForm">
                        @csrf

                        <div class="mb-5">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Nomor Induk Kependudukan (NIK)</label>
                            <input type="number" name="nik" class="form-control form-control-lg fs-5 fw-bold"
                                   placeholder="16 digit angka sesuai KTP"
                                   value="{{ old('nik') }}"
                                   required style="height: 60px;">
                            @error('nik')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">1. Foto KTP Asli</label>
                                <div class="upload-container" id="container-ktp" onclick="triggerUpload('ktpInput')">
                                    <div class="upload-placeholder" id="placeholder-ktp">
                                        <i class="fas fa-id-card upload-icon"></i>
                                        <div class="fw-bold text-dark">Upload KTP</div>
                                        <div class="small text-muted">Format: JPG/PNG (Max 5MB)</div>
                                    </div>
                                    <img id="preview-ktp" class="img-preview">
                                    <button type="button" class="btn-remove-img shadow-sm" id="remove-ktp" onclick="removeImage(event, 'ktpInput', 'preview-ktp', 'placeholder-ktp', 'remove-ktp', 'container-ktp')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" name="ktp_image" id="ktpInput" class="d-none" accept="image/*" onchange="previewImage(this, 'preview-ktp', 'placeholder-ktp', 'remove-ktp', 'container-ktp')" required>
                                @error('ktp_image') <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">2. Foto Selfie dengan KTP</label>
                                <div class="upload-container" id="container-selfie" onclick="triggerUpload('selfieInput')">
                                    <div class="upload-placeholder" id="placeholder-selfie">
                                        <i class="fas fa-camera-retro upload-icon"></i>
                                        <div class="fw-bold text-dark">Ambil Selfie</div>
                                        <div class="small text-muted">Wajah & KTP harus terlihat jelas</div>
                                    </div>
                                    <img id="preview-selfie" class="img-preview">
                                    <button type="button" class="btn-remove-img shadow-sm" id="remove-selfie" onclick="removeImage(event, 'selfieInput', 'preview-selfie', 'placeholder-selfie', 'remove-selfie', 'container-selfie')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" name="selfie_image" id="selfieInput" class="d-none" accept="image/*" onchange="previewImage(this, 'preview-selfie', 'placeholder-selfie', 'remove-selfie', 'container-selfie')" required>
                                @error('selfie_image') <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-primary bg-opacity-10 text-primary small d-flex align-items-center mb-4 rounded-3 p-3">
                            <i class="fas fa-robot fs-4 me-3"></i>
                            <div>
                                <strong>AI Verification System:</strong> Sistem kami akan mencocokkan wajah Anda secara otomatis. Pastikan pencahayaan cukup.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-submit-kyc btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> KIRIM DATA VERIFIKASI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function triggerUpload(inputId) {
        document.getElementById(inputId).click();
    }

    function previewImage(input, imgId, placeholderId, btnRemoveId, containerId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                img.src = e.target.result;
                img.style.display = 'block';
                document.getElementById(placeholderId).style.opacity = '0';
                document.getElementById(btnRemoveId).style.display = 'flex';
                document.getElementById(containerId).classList.add('has-image');
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage(event, inputId, imgId, placeholderId, btnRemoveId, containerId) {
        event.stopPropagation();
        document.getElementById(inputId).value = "";
        const img = document.getElementById(imgId);
        img.style.display = 'none';
        img.src = "";
        document.getElementById(placeholderId).style.opacity = '1';
        document.getElementById(btnRemoveId).style.display = 'none';
        document.getElementById(containerId).classList.remove('has-image');
    }

    // Submit Handler with Progress Animation
    document.getElementById('kycForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Stage 1: Validation
        Swal.fire({
            title: 'Memeriksa Data',
            html: `
                <div class="mb-3 text-muted">Sedang memvalidasi NIK dan mengunggah dokumen...</div>
                <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e9ecef;">
                    <div id="kyc-progress-1" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                </div>
                <div class="mt-3 text-muted small fst-italic">Mohon tunggu sebentar...</div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                const progressBar1 = document.getElementById('kyc-progress-1');
                let width = 0;
                const timer1 = setInterval(() => {
                    if (width < 60) {
                        width += 5;
                        if (progressBar1) progressBar1.style.width = width + '%';
                    } else {
                        clearInterval(timer1);
                        startAiAnalysis(); // Move to Stage 2
                    }
                }, 100);
            }
        });

        function startAiAnalysis() {
            Swal.update({
                title: 'Analisis AI Berjalan',
                html: `
                    <div class="mb-3 text-muted">Sistem AI sedang memindai biometrik wajah...</div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e9ecef;">
                        <div id="kyc-progress-2" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 60%"></div>
                    </div>
                    <div class="mt-3 text-muted small fst-italic">Proses ini membutuhkan waktu beberapa detik.</div>
                `
            });

            const progressBar2 = document.getElementById('kyc-progress-2');
            let width = 60;
            const timer2 = setInterval(() => {
                if (width < 95) {
                    width += 2;
                    if (progressBar2) progressBar2.style.width = width + '%';
                }
            }, 300);

            setTimeout(() => {
                clearInterval(timer2);
                e.target.submit();
            }, 1500);
        }
    });

    // Error Feedback
    @if($errors->has('nik'))
        Swal.fire({
            icon: 'error',
            title: 'Verifikasi Gagal',
            text: '{{ $errors->first('nik') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Periksa Kembali'
        });
    @endif

    @if($errors->has('ktp_image') || $errors->has('selfie_image'))
        Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: 'Pastikan format foto JPG/PNG dan ukuran di bawah 5MB.',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endpush
@endsection
