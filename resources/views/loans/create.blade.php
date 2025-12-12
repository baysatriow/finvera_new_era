@extends('layouts.dashboard')

@section('page_title', 'Ajukan Pinjaman Baru')

@section('content')
<style>
    /* --- Styling Chips --- */
    .nominal-chip {
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e0e0e0;
        background-color: white;
        color: #555;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.9rem;
    }
    .nominal-chip:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
    }
    .nominal-chip.active {
        background-color: #e8f5e9;
        border-color: #3A6D48;
        color: #3A6D48;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(58, 109, 72, 0.15);
    }

    /* --- Styling Slider Modern --- */
    input[type=range] {
        -webkit-appearance: none;
        width: 100%;
        background: transparent;
        margin: 10px 0;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #3A6D48;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        cursor: pointer;
        margin-top: -8px;
    }
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 6px;
        cursor: pointer;
        background: #d1e7dd;
        border-radius: 3px;
    }
    input[type=range]:focus {
        outline: none;
    }

    /* --- Upload Preview (Updated) --- */
    .img-preview-container {
        width: 100%;
        height: 240px;
        background-color: #fcfcfc;
        border: 2px dashed #dee2e6;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        transition: border-color 0.3s;
        cursor: pointer;
    }
    .img-preview-container:hover {
        border-color: #3A6D48;
        background-color: #f4fcf6;
    }
    .img-preview-container.has-image {
        border-style: solid;
        border-color: #3A6D48;
        background-color: #212529; /* Background gelap agar foto jelas */
    }

    /* Image Style: Fit Contain & Center */
    .img-preview {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        background-color: #333;
    }

    .upload-placeholder {
        text-align: center;
        width: 100%;
        transition: opacity 0.2s;
    }
    .upload-icon {
        color: #adb5bd;
        font-size: 3rem;
        margin-bottom: 15px;
    }

    /* Tombol Hapus Foto (X) */
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
        display: none; /* Hidden by default */
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

    /* --- Akad List --- */
    .akad-list-group {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    .akad-item {
        padding: 20px 25px;
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        gap: 20px;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    .akad-item:last-child {
        border-bottom: none;
    }
    .akad-item:hover {
        background-color: #f8f9fa;
    }
    .form-check-input.akad-check {
        margin-top: 5px;
        border: 2px solid #adb5bd;
        width: 1.5em;
        height: 1.5em;
        flex-shrink: 0;
        cursor: pointer;
    }
    .form-check-input.akad-check:checked {
        background-color: #3A6D48;
        border-color: #3A6D48;
    }
</style>

<div class="row">
    <div class="col-12">

        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <!-- Header Card -->
            <div class="card-header bg-white py-4 px-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 flex-shrink-0">
                        <i class="fas fa-hand-holding-usd fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Formulir Pengajuan Pembiayaan</h5>
                        <p class="text-muted small mb-0">Isi data di bawah ini untuk mengajukan pembiayaan Qardh.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data" id="loanForm">
                    @csrf
                    <input type="hidden" name="loan_product_id" value="{{ $product->id }}">

                    <div class="row g-5">
                        <!-- BAGIAN KIRI: Form Input -->
                        <div class="col-lg-7">

                            <!-- 1. NOMINAL PINJAMAN -->
                            <div class="mb-5">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Nominal Pinjaman</label>

                                <div class="input-group input-group-lg mb-2 shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 fw-bold text-success ps-4">Rp</span>
                                    <!-- Input Display (Format Ribuan) -->
                                    <input type="text" class="form-control border-start-0 fw-bold text-dark" id="amountDisplay" placeholder="0" style="font-size: 2rem; height: 60px;">
                                    <!-- Input Hidden (Nilai Asli) -->
                                    <input type="hidden" name="amount" id="amountInput" value="1000000">
                                </div>

                                <!-- Slider Nominal -->
                                <input type="range" class="form-range" min="1000000" max="20000000" step="100000" id="amountSlider" value="1000000">

                                <!-- Template Chips -->
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <div class="nominal-chip active" data-val="1000000">1 Juta</div>
                                    <div class="nominal-chip" data-val="3000000">3 Juta</div>
                                    <div class="nominal-chip" data-val="5000000">5 Juta</div>
                                    <div class="nominal-chip" data-val="10000000">10 Juta</div>
                                    <div class="nominal-chip" data-val="15000000">15 Juta</div>
                                    <div class="nominal-chip" data-val="20000000">20 Juta</div>
                                </div>
                                <div class="form-text text-muted small mt-2"><i class="fas fa-info-circle me-1"></i> Limit maksimal Anda: Rp 20.000.000</div>
                            </div>

                            <!-- 2. TENOR FLUID -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-0">Jangka Waktu Pengembalian</label>
                                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill" id="tenorLabel">1 Bulan</span>
                                </div>
                                <input type="range" class="form-range" min="1" max="12" step="1" id="tenorRange" name="tenor" value="1">
                                <div class="d-flex justify-content-between small text-muted fw-bold mt-1">
                                    <span>1 Bulan</span>
                                    <span id="maxTenorLabel">12 Bulan</span>
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            <!-- 3. TUJUAN & ASET -->
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <!-- Ikon diperbesar dan padding disesuaikan agar tidak gepeng -->
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-contract fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Detail & Jaminan</h5>
                                    <p class="text-muted small mb-0">Informasi penggunaan dana dan agunan</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Tujuan Peminjaman</label>
                                <textarea name="purpose" class="form-control bg-light" rows="3" placeholder="Jelaskan secara rinci penggunaan dana (Misal: Tambahan modal usaha warung sembako...)" required style="border: 1px solid #eee;"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Jenis Aset Jaminan</label>
                                    <select name="asset_type" class="form-select form-select-lg fs-6" required>
                                        <option value="">Pilih Jenis Aset...</option>
                                        <option value="BPKB Motor">BPKB Kendaraan Roda Dua</option>
                                        <option value="BPKB Mobil">BPKB Kendaraan Roda Empat</option>
                                        <option value="Sertifikat Rumah/Tanah">Sertifikat Hak Milik (SHM)</option>
                                        <option value="Logam Mulia (Emas)">Logam Mulia / Emas Antam</option>
                                        <option value="Elektronik">Elektronik Bernilai Tinggi</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Estimasi Nilai Aset</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light fs-6">Rp</span>
                                        <!-- Input Asset dengan Auto Format -->
                                        <input type="text" class="form-control fw-bold fs-6" id="assetDisplay" placeholder="0" required>
                                        <input type="hidden" name="asset_value" id="assetInput">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. UPLOAD -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Foto Dokumen Aset</label>
                                    <div class="img-preview-container" id="container-doc" onclick="triggerUpload('docInput')">
                                        <div class="upload-placeholder text-center" id="docPlaceholder">
                                            <i class="fas fa-file-invoice upload-icon"></i>
                                            <div class="fw-bold text-dark">Upload Dokumen</div>
                                            <div class="small text-muted mt-1">Format: JPG, PNG (Max 4MB)</div>
                                        </div>
                                        <img id="docPreview" class="img-preview">
                                        <button type="button" class="btn-remove-img shadow-sm" id="remove-doc" onclick="removeImage(event, 'docInput', 'docPreview', 'docPlaceholder', 'remove-doc', 'container-doc')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <input type="file" name="asset_document" id="docInput" class="d-none" accept="image/*" onchange="previewImage(this, 'docPreview', 'docPlaceholder', 'remove-doc', 'container-doc')" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Selfie dengan Aset</label>
                                    <div class="img-preview-container" id="container-selfie" onclick="triggerUpload('selfieInput')">
                                        <div class="upload-placeholder text-center" id="selfiePlaceholder">
                                            <i class="fas fa-camera-retro upload-icon"></i>
                                            <div class="fw-bold text-dark">Ambil Foto Selfie</div>
                                            <div class="small text-muted mt-1">Wajah & Aset harus terlihat</div>
                                        </div>
                                        <img id="selfiePreview" class="img-preview">
                                        <button type="button" class="btn-remove-img shadow-sm" id="remove-selfie" onclick="removeImage(event, 'selfieInput', 'selfiePreview', 'selfiePlaceholder', 'remove-selfie', 'container-selfie')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <input type="file" name="asset_selfie" id="selfieInput" class="d-none" accept="image/*" onchange="previewImage(this, 'selfiePreview', 'selfiePlaceholder', 'remove-selfie', 'container-selfie')" required>
                                </div>
                            </div>

                        </div>

                        <!-- BAGIAN KANAN: Simulasi & Submit -->
                        <div class="col-lg-5">
                            <div class="sticky-top" style="top: 100px; z-index: 1;">

                                <!-- Card Simulasi -->
                                <div class="card bg-success text-white border-0 shadow-lg rounded-4 mb-4 overflow-hidden position-relative">
                                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                        <i class="fas fa-calculator fa-5x"></i>
                                    </div>
                                    <div class="card-body p-4 position-relative">
                                        <h6 class="text-white-50 text-uppercase fw-bold mb-4" style="letter-spacing: 1px;">Simulasi Pembayaran</h6>

                                        <div class="mb-4">
                                            <small class="d-block text-white-50 mb-1">Estimasi Cicilan Bulanan</small>
                                            <h2 class="fw-bold mb-0" id="monthlyInstallment">Rp 1.000.000</h2>
                                        </div>

                                        <div class="border-top border-white border-opacity-25 pt-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-white-50">Pokok Pinjaman</span>
                                                <span class="fw-bold" id="pokokDisplay">Rp 1.000.000</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-white-50">Bunga/Riba</span>
                                                <span class="badge bg-white text-success fw-bold">Rp 0 (0%)</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-white-50"><i class="fas fa-info-circle me-1"></i> Biaya Admin</span>
                                                <span class="fw-bold">Rp {{ number_format($product->admin_fee, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Akad -->
                                <div class="card bg-white border shadow-sm rounded-4">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                                                <i class="fas fa-file-signature fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">Persetujuan Akad</h6>
                                                <p class="small text-muted mb-0 lh-sm">Wajib menyetujui akad sebelum lanjut.</p>
                                            </div>
                                        </div>

                                        <input type="checkbox" name="tos_agreement" id="tos_agreement" class="d-none" required>

                                        <button type="button" class="btn btn-outline-dark w-100 fw-bold py-2 mb-3" id="btnOpenAkad">
                                            Baca & Setujui Akad <i class="fas fa-chevron-right ms-1"></i>
                                        </button>

                                        <div id="akadSuccessMsg" class="alert alert-success d-flex align-items-center py-2 px-3 small d-none mb-3">
                                            <i class="fas fa-check-circle me-2"></i> Akad Telah Disetujui
                                        </div>

                                        <button type="submit" class="btn btn-success w-100 shadow fw-bold py-3" id="btnSubmit" disabled>
                                            <i class="fas fa-paper-plane me-2"></i> KIRIM PENGAJUAN
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL AKAD (TABEL CHECKLIST) -->
<div class="modal fade" id="akadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white py-3" style="background-color: #3A6D48;">
                <h5 class="modal-title fw-bold"><i class="fas fa-scroll me-2"></i>Syarat & Ketentuan Akad Qardh</h5>
            </div>
            <div class="modal-body bg-light p-4">

                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                    <i class="fas fa-info-circle fs-4 me-3"></i>
                    <div>
                        Silakan baca dan <strong>centang setiap poin</strong> di bawah ini untuk mengonfirmasi pemahaman Anda terhadap prinsip syariah yang kami terapkan.
                    </div>
                </div>

                <!-- List Akad -->
                <div class="akad-list-group bg-white shadow-sm mb-4">

                    <label class="akad-item" for="checkA">
                        <input type="checkbox" class="form-check-input akad-check" id="checkA">
                        <div class="akad-content">
                            <h6>A. Prinsip Tanpa Riba (Qardh)</h6>
                            <p>Saya memahami bahwa fasilitas ini adalah Qardh (pinjaman kebajikan) yang <strong>bebas bunga, margin, maupun penalti komersial</strong>. Saya hanya diwajibkan mengembalikan pokok pinjaman.</p>
                        </div>
                    </label>

                    <label class="akad-item" for="checkB">
                        <input type="checkbox" class="form-check-input akad-check" id="checkB">
                        <div class="akad-content">
                            <h6>B. Biaya Administrasi & Tenor</h6>
                            <p>Saya menyetujui adanya <strong>Biaya Administrasi (Ujrah)</strong> nominal tetap yang dibebankan satu kali di awal untuk keperluan operasional verifikasi dan teknologi, bukan dihitung sebagai persentase pinjaman.</p>
                        </div>
                    </label>

                    <label class="akad-item" for="checkC">
                        <input type="checkbox" class="form-check-input akad-check" id="checkC">
                        <div class="akad-content">
                            <h6>C. Mekanisme Persetujuan</h6>
                            <p>Saya mengerti bahwa persetujuan didasarkan pada analisis AI dan verifikasi manual. Perusahaan berhak menolak pengajuan jika tidak memenuhi kriteria risiko syariah.</p>
                        </div>
                    </label>

                    <label class="akad-item" for="checkD">
                        <input type="checkbox" class="form-check-input akad-check" id="checkD">
                        <div class="akad-content">
                            <h6>D. Pencairan Dana</h6>
                            <p>Dana akan dicairkan maksimal 24 jam kerja ke rekening bank yang telah saya daftarkan dan verifikasi atas nama saya sendiri.</p>
                        </div>
                    </label>

                    <label class="akad-item" for="checkE">
                        <input type="checkbox" class="form-check-input akad-check" id="checkE">
                        <div class="akad-content">
                            <h6>E. Komitmen Pembayaran & Ta'zir</h6>
                            <p>Saya berjanji melunasi tepat waktu. Jika lalai, saya bersedia dikenakan <strong>Ta'zir (Sanksi)</strong> berupa dana sosial yang TIDAK menjadi pendapatan perusahaan, melainkan disalurkan untuk amal.</p>
                        </div>
                    </label>

                    <label class="akad-item" for="checkF">
                        <input type="checkbox" class="form-check-input akad-check" id="checkF">
                        <div class="akad-content">
                            <h6>F. Larangan Penyalahgunaan</h6>
                            <p>Saya menjamin data yang diberikan asli dan dana tidak digunakan untuk kegiatan yang melanggar hukum negara atau prinsip syariah (seperti perjudian/spekulasi).</p>
                        </div>
                    </label>

                </div>

                <div class="card bg-white border-success border-2 shadow-sm">
                    <div class="card-body d-flex align-items-center p-4">
                        <input type="checkbox" class="form-check-input fs-4 mt-0 me-3 border-success" id="checkFinal" disabled style="cursor: not-allowed; width: 1.5em; height: 1.5em;">
                        <div>
                            <label class="form-check-label fw-bold text-dark fs-6" for="checkFinal" style="opacity: 0.6;" id="labelFinal">
                                PERNYATAAN PERSETUJUAN
                            </label>
                            <div class="text-muted small">Dengan ini saya menyetujui seluruh ketentuan akad Qardh di atas dan siap mengajukan permohonan.</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-lg btn-secondary w-100 fw-bold" id="btnConfirmAkad" disabled>
                    <i class="fas fa-lock me-2"></i> Lengkapi Semua Checklist Di Atas
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Trigger Input File
    function triggerUpload(inputId) {
        document.getElementById(inputId).click();
    }

    // --- 1. LOGIC NOMINAL & SLIDER ---
    const amountInput = document.getElementById('amountInput'); // Hidden Integer
    const amountDisplay = document.getElementById('amountDisplay'); // Text Display
    const amountSlider = document.getElementById('amountSlider'); // Slider
    const tenorRange = document.getElementById('tenorRange');
    const tenorLabel = document.getElementById('tenorLabel');
    const maxTenorLabel = document.getElementById('maxTenorLabel');
    const monthlyDisplay = document.getElementById('monthlyInstallment');
    const pokokDisplay = document.getElementById('pokokDisplay');

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function cleanRupiah(angka) {
        return parseInt(angka.replace(/\./g, '')) || 0;
    }

    function updateCalculations() {
        let amount = parseInt(amountInput.value) || 0;
        let tenor = parseInt(tenorRange.value) || 1;

        let maxTenor = 12;
        if (amount <= 2000000) maxTenor = 3;
        else if (amount <= 5000000) maxTenor = 6;
        else if (amount <= 10000000) maxTenor = 12;
        else maxTenor = 24;

        tenorRange.max = maxTenor;
        maxTenorLabel.innerText = maxTenor + " Bulan";

        if (parseInt(tenorRange.value) > maxTenor) {
            tenorRange.value = maxTenor;
            tenor = maxTenor; // Fix immediate calculation
        }

        let monthly = Math.ceil(amount / tenor);
        const fmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });

        tenorLabel.innerText = tenor + " Bulan";
        monthlyDisplay.innerText = fmt.format(monthly);
        pokokDisplay.innerText = fmt.format(amount);

        document.querySelectorAll('.nominal-chip').forEach(chip => {
            chip.classList.remove('active');
            if(parseInt(chip.dataset.val) === amount) chip.classList.add('active');
        });
    }

    amountDisplay.addEventListener('input', function() {
        let val = cleanRupiah(this.value);
        amountInput.value = val;
        amountSlider.value = val;
        this.value = formatRupiah(val);
        updateCalculations();
    });

    amountSlider.addEventListener('input', function() {
        let val = parseInt(this.value);
        amountInput.value = val;
        amountDisplay.value = formatRupiah(val);
        updateCalculations();
    });

    document.querySelectorAll('.nominal-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            let val = parseInt(this.dataset.val);
            amountInput.value = val;
            amountDisplay.value = formatRupiah(val);
            amountSlider.value = val;
            updateCalculations();
        });
    });

    tenorRange.addEventListener('input', updateCalculations);

    // --- 2. ASSET VALUE FORMATTER ---
    const assetDisplay = document.getElementById('assetDisplay');
    const assetInput = document.getElementById('assetInput');

    assetDisplay.addEventListener('input', function() {
        let val = cleanRupiah(this.value);
        assetInput.value = val;
        this.value = formatRupiah(val);
    });

    // --- 3. IMAGE PREVIEW (Fixed) ---
    window.previewImage = function(input, imgId, placeholderId, btnRemoveId, containerId) {
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

    window.removeImage = function(event, inputId, imgId, placeholderId, btnRemoveId, containerId) {
        event.stopPropagation();
        document.getElementById(inputId).value = "";
        const img = document.getElementById(imgId);
        img.style.display = 'none';
        img.src = "";
        document.getElementById(placeholderId).style.opacity = '1';
        document.getElementById(btnRemoveId).style.display = 'none';
        document.getElementById(containerId).classList.remove('has-image');
    }

    // --- 4. AKAD CHECKLIST ---
    const akadModal = new bootstrap.Modal(document.getElementById('akadModal'));
    const btnOpenAkad = document.getElementById('btnOpenAkad');
    const checkPoints = document.querySelectorAll('.akad-check');
    const checkFinal = document.getElementById('checkFinal');
    const labelFinal = document.getElementById('labelFinal');
    const btnConfirmAkad = document.getElementById('btnConfirmAkad');
    const mainSubmitBtn = document.getElementById('btnSubmit');
    const akadSuccessMsg = document.getElementById('akadSuccessMsg');
    const hiddenTosInput = document.getElementById('tos_agreement');

    btnOpenAkad.addEventListener('click', () => akadModal.show());

    checkPoints.forEach(chk => {
        chk.addEventListener('change', () => {
            const allChecked = Array.from(checkPoints).every(c => c.checked);

            if (allChecked) {
                checkFinal.disabled = false;
                checkFinal.style.cursor = 'pointer';
                labelFinal.style.opacity = '1';
                labelFinal.classList.add('text-success');
            } else {
                checkFinal.disabled = true;
                checkFinal.checked = false;
                checkFinal.style.cursor = 'not-allowed';
                labelFinal.style.opacity = '0.6';
                labelFinal.classList.remove('text-success');
                btnConfirmAkad.disabled = true;
                btnConfirmAkad.classList.remove('btn-success');
                btnConfirmAkad.classList.add('btn-secondary');
                btnConfirmAkad.innerHTML = '<i class="fas fa-lock me-2"></i> Lengkapi Semua Checklist Di Atas';
            }
        });
    });

    checkFinal.addEventListener('change', function() {
        if (this.checked) {
            btnConfirmAkad.disabled = false;
            btnConfirmAkad.classList.remove('btn-secondary');
            btnConfirmAkad.classList.add('btn-success');
            btnConfirmAkad.innerHTML = '<i class="fas fa-signature me-2"></i> SAYA SETUJU & LANJUTKAN';
        } else {
            btnConfirmAkad.disabled = true;
            btnConfirmAkad.classList.remove('btn-success');
            btnConfirmAkad.classList.add('btn-secondary');
        }
    });

    btnConfirmAkad.addEventListener('click', () => {
        hiddenTosInput.checked = true;
        mainSubmitBtn.disabled = false;
        akadSuccessMsg.classList.remove('d-none');
        btnOpenAkad.classList.add('d-none');
        akadModal.hide();
    });

    // --- 5. LOADING ANIMATION ---
    document.getElementById('loanForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'FinVera AI Processing',
            html: '<div class="my-2">Menganalisis Profil Risiko & Aset...</div><div class="progress" style="height: 5px;"><div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            width: 400,
            padding: '2em',
            backdrop: `rgba(0,0,0,0.8)`
        });
    });

    // Init Defaults
    amountDisplay.value = formatRupiah(amountInput.value);
    updateCalculations();

</script>
@endpush
@endsection
