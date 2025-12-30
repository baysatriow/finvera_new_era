@extends('layouts.dashboard')

@section('page_title', 'Pembayaran Cicilan')

@section('content')
<style>
    /* Card Styles */
    .pay-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: white;
        overflow: hidden;
    }

    /* Header Gradient */
    .amount-header {
        background: linear-gradient(135deg, #3A6D48 0%, #2c5236 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    /* Bank Item */
    .bank-item {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.2s;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .bank-item:hover {
        border-color: #3A6D48;
        background-color: #f9fdfa;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    /* Upload Container Fixed */
    .upload-container {
        width: 100%;
        height: 250px;
        background-color: #fcfcfc;
        border: 2px dashed #ced4da;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        transition: all 0.3s;
        cursor: pointer;
    }
    .upload-container:hover {
        border-color: #3A6D48;
        background-color: #f1f8f3;
    }
    .upload-container.has-image {
        border-style: solid;
        border-color: #3A6D48;
        background-color: #212529; /* Background gelap agar foto jelas */
    }

    /* Image Preview (Center & Contain) */
    .img-preview {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: none;
        position: absolute;
        top: 0;
        left: 0;
    }

    /* Upload Placeholder */
    .upload-placeholder {
        text-align: center;
        transition: opacity 0.2s;
        pointer-events: none;
    }

    /* Tombol Hapus X */
    .btn-remove-img {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #dc3545;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 10;
        transition: transform 0.2s;
    }
    .btn-remove-img:hover {
        transform: scale(1.1);
        background: white;
    }

    /* Tombol Aksi */
    .btn-confirm {
        background-color: #3A6D48;
        color: white;
        border: none;
        padding: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 50px;
        transition: all 0.3s;
        width: 100%;
    }
    .btn-confirm:hover {
        background-color: #2c5236;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(58, 109, 72, 0.3);
        color: white;
    }

    /* Tombol Batal (Visible) */
    .btn-cancel {
        background-color: white;
        color: #dc3545;
        border: 2px solid #dc3545;
        padding: 12px;
        font-weight: 700;
        border-radius: 50px;
        transition: all 0.2s;
        width: 100%;
        display: block;
        text-align: center;
        text-decoration: none;
    }
    .btn-cancel:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- Header Total Tagihan -->
        <div class="card pay-card mb-4">
            <div class="amount-header">
                <i class="fas fa-wallet position-absolute text-white" style="opacity: 0.1; font-size: 8rem; right: -20px; bottom: -20px;"></i>
                <h6 class="text-white-50 text-uppercase fw-bold small mb-2 ls-1">Total Tagihan Bulan Ke-{{ $installment->installment_number }}</h6>
                <h1 class="display-4 fw-bold text-white mb-2">Rp {{ number_format($installment->amount + $installment->tazir_amount, 0, ',', '.') }}</h1>
                
                @if($installment->tazir_amount > 0)
                    <div class="d-inline-block bg-white bg-opacity-10 rounded-pill px-3 py-1 mb-2">
                        <small class="text-warning fw-bold">
                            (Pokok: Rp {{ number_format($installment->amount, 0, ',', '.') }} + Ta'zir: Rp {{ number_format($installment->tazir_amount, 0, ',', '.') }})
                        </small>
                    </div>
                @endif

                <div class="badge bg-white text-danger px-3 py-2 rounded-pill mt-2 fw-bold d-block mx-auto" style="width: fit-content;">
                    <i class="fas fa-clock me-1"></i> Jatuh Tempo: {{ $installment->due_date->format('d F Y') }}
                </div>
            </div>
        </div>

        <form action="{{ route('installments.submit', $installment->id) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
            @csrf

            <div class="row g-4">
                <!-- Kolom Kiri: Instruksi Transfer -->
                <div class="col-lg-7">
                    <div class="card pay-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                    <i class="fas fa-university fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-0">Metode Transfer Bank</h6>
                            </div>

                            <p class="small text-muted mb-4">Silakan transfer nominal tagihan <strong>tepat hingga 3 digit terakhir</strong> ke salah satu rekening resmi di bawah ini:</p>

                            @foreach($adminBanks as $bank)
                            <div class="bank-item">
                                <div>
                                    <div class="fw-bold text-dark">{{ $bank->bank_name }}</div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $bank->account_holder }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="font-monospace fw-bold fs-5 text-dark" id="rek-{{ $loop->index }}">{{ $bank->account_number }}</div>
                                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold text-success small" onclick="copyToClipboard('rek-{{ $loop->index }}')">
                                        <i class="far fa-copy me-1"></i> Salin
                                    </button>
                                </div>
                            </div>
                            @endforeach

                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 small mt-4 mb-0 rounded-3 d-flex align-items-center text-dark">
                                <i class="fas fa-info-circle me-3 fs-4 text-warning"></i>
                                <div>
                                    <strong>Penting:</strong> Simpan bukti transfer Anda. Verifikasi akan diproses oleh Admin dalam 1x24 jam kerja.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Upload Bukti -->
                <div class="col-lg-5">
                    <div class="card pay-card h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-receipt fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-0">Konfirmasi Pembayaran</h6>
                            </div>

                            <div class="mb-4 flex-grow-1">
                                <label class="form-label small text-muted fw-bold mb-2">Upload Bukti Transfer</label>

                                <div class="upload-container" id="container-proof" onclick="triggerUpload()">
                                    <div class="upload-placeholder" id="placeholder-proof">
                                        <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-2 d-block"></i>
                                        <div class="fw-bold text-dark">Pilih File Bukti</div>
                                        <small class="text-muted d-block mt-1">JPG, PNG, PDF (Max 5MB)</small>
                                    </div>

                                    <img id="preview-proof" class="img-preview">

                                    <button type="button" class="btn-remove-img shadow-sm" id="remove-proof" onclick="removeImage(event)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" name="proof_file" id="proofInput" class="d-none" accept="image/*,application/pdf" onchange="previewFile(this)" required>
                            </div>

                            <div class="d-grid gap-3 mt-auto">
                                <button type="button" class="btn btn-confirm shadow-sm" id="btnSubmit">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Konfirmasi
                                </button>
                                <a href="{{ route('installments.index') }}" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i> Batalkan
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Copy Clipboard
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(copyText).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: 'Nomor rekening berhasil disalin.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    // Trigger Upload
    function triggerUpload() {
        document.getElementById('proofInput').click();
    }

    // Preview Logic
    function previewFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-proof');
                img.src = e.target.result;
                img.style.display = 'block';

                document.getElementById('placeholder-proof').style.opacity = '0';
                document.getElementById('remove-proof').style.display = 'flex';
                document.getElementById('container-proof').classList.add('has-image');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Remove Image
    function removeImage(event) {
        event.stopPropagation();
        document.getElementById('proofInput').value = "";

        const img = document.getElementById('preview-proof');
        img.style.display = 'none';
        img.src = "";

        document.getElementById('placeholder-proof').style.opacity = '1';
        document.getElementById('remove-proof').style.display = 'none';
        document.getElementById('container-proof').classList.remove('has-image');
    }

    // SWAL Confirmation Submit
    document.getElementById('btnSubmit').addEventListener('click', function(e) {
        e.preventDefault();

        // Cek file
        const fileInput = document.getElementById('proofInput');
        if (!fileInput.files.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Bukti',
                text: 'Silakan upload bukti transfer terlebih dahulu!',
                confirmButtonColor: '#3A6D48'
            });
            return;
        }

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Pastikan nominal transfer dan bukti pembayaran sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3A6D48',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim Sekarang!',
            cancelButtonText: 'Cek Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('paymentForm').submit();
            }
        });
    });
</script>
@endpush
@endsection
