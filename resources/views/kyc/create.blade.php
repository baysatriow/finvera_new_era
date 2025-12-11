@extends('layouts.dashboard')

@section('page_title', 'Identitas & Verifikasi (KYC)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        @if($user->kyc_status == 'verified' || $user->kyc_status == 'pending')
            <!-- TAMPILAN DATA (READ ONLY) -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        @if($user->kyc_status == 'verified')
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-shield-alt fa-3x"></i>
                            </div>
                            <h4 class="fw-bold text-success">Akun Terverifikasi</h4>
                            <p class="text-muted">Identitas Anda telah diverifikasi dan aman.</p>

                            <div class="alert alert-success d-inline-block px-4 py-2 border-0 shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Data Sinkron dan Akurat
                            </div>
                        @else
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-clock fa-3x"></i>
                            </div>
                            <h4 class="fw-bold text-warning">Sedang Dalam Peninjauan</h4>
                            <p class="text-muted">Data Anda sedang diproses oleh sistem AI dan Admin kami.</p>
                        @endif
                    </div>

                    <div class="row g-4 mt-2">
                        <!-- Data Teks -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted fw-bold text-uppercase d-block mb-1">Nomor Induk Kependudukan (NIK)</label>
                                <div class="fw-bold fs-5 text-dark letter-spacing-1">{{ $user->kyc->nik ?? '-' }}</div>
                            </div>
                        </div>

                        <!-- Foto Dokumen -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white fw-bold small text-muted">Foto KTP</div>
                                <div class="card-body p-2 text-center bg-light">
                                    @if($user->kyc && $user->kyc->ktp_image_path)
                                        <img src="{{ asset('storage/' . $user->kyc->ktp_image_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;" alt="Foto KTP">
                                    @else
                                        <div class="py-5 text-muted">Gambar tidak tersedia</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white fw-bold small text-muted">Foto Selfie</div>
                                <div class="card-body p-2 text-center bg-light">
                                    @if($user->kyc && $user->kyc->selfie_image_path)
                                        <img src="{{ asset('storage/' . $user->kyc->selfie_image_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;" alt="Foto Selfie">
                                    @else
                                        <div class="py-5 text-muted">Gambar tidak tersedia</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <p class="small text-muted mb-0">
                            <i class="fas fa-lock me-1"></i> Data Anda dilindungi dengan enkripsi tingkat tinggi dan tidak akan dibagikan ke pihak ketiga tanpa persetujuan.
                        </p>
                    </div>
                </div>
            </div>

        @else
            <!-- TAMPILAN FORM UPLOAD (UNVERIFIED / REJECTED) -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">

                    <div class="text-center mb-5">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-id-card fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">Verifikasi Identitas</h4>
                        <p class="text-muted">Lengkapi data di bawah ini untuk mengaktifkan fitur pinjaman.</p>
                        @if($user->kyc_status == 'rejected')
                            <div class="alert alert-danger">
                                <strong>Verifikasi Ditolak:</strong> {{ $user->kyc->rejection_reason ?? 'Foto tidak jelas.' }} <br>Silakan upload ulang.
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" class="form-control form-control-lg" placeholder="16 digit angka sesuai KTP" maxlength="16" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold small text-uppercase text-muted">Foto KTP</label>
                                <div class="card border-dashed bg-light text-center h-100 p-3" onclick="document.getElementById('ktpInput').click()" style="cursor: pointer; border: 2px dashed #dee2e6;">
                                    <div class="py-4">
                                        <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                                        <p class="small text-muted mb-0">Klik untuk upload KTP</p>
                                    </div>
                                    <input type="file" name="ktp_image" id="ktpInput" class="d-none" accept="image/*" onchange="previewFile(this, 'ktpPreview')" required>
                                    <img id="ktpPreview" class="img-fluid rounded mt-2 d-none" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Foto Selfie dengan KTP</label>
                                <div class="card border-dashed bg-light text-center h-100 p-3" onclick="document.getElementById('selfieInput').click()" style="cursor: pointer; border: 2px dashed #dee2e6;">
                                    <div class="py-4">
                                        <i class="fas fa-user-check fa-2x text-muted mb-2"></i>
                                        <p class="small text-muted mb-0">Klik untuk upload Selfie</p>
                                    </div>
                                    <input type="file" name="selfie_image" id="selfieInput" class="d-none" accept="image/*" onchange="previewFile(this, 'selfiePreview')" required>
                                    <img id="selfiePreview" class="img-fluid rounded mt-2 d-none" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small d-flex gap-2 align-items-center">
                            <i class="fas fa-robot fs-4"></i>
                            <div>
                                <strong>AI Powered Verification:</strong> Sistem kami akan mencocokkan wajah Anda secara otomatis. Pastikan foto terlihat jelas dan tidak buram.
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-finvera btn-lg shadow-sm fw-bold">
                                Kirim Data Verifikasi
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
    function previewFile(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection
