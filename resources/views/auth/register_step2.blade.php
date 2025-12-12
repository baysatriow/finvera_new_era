@extends('layouts.auth')

@section('title', 'Lengkapi Data Diri')

@section('content')
<style>
    /* Styling Select2 & Form */
    .select2-container .select2-selection--single {
        height: 50px !important;
        padding: 10px 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        color: #212529;
        font-size: 1rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
        right: 15px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: var(--finvera-primary);
        box-shadow: 0 0 0 0.2rem rgba(58, 109, 72, 0.25);
    }
    .form-control, .form-select {
        height: 50px;
    }
    textarea.form-control {
        height: auto;
    }

    /* Styling Range Slider */
    input[type=range]::-webkit-slider-thumb {
        background: var(--finvera-primary);
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        height: 20px;
        width: 20px;
        margin-top: -8px;
        border-radius: 50%;
        cursor: pointer;
        -webkit-appearance: none;
    }
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 6px;
        cursor: pointer;
        background: #d1e7dd;
        border-radius: 3px;
    }

    /* Styling TOS Link */
    .tos-link {
        color: var(--finvera-primary);
        font-weight: 700;
        text-decoration: underline;
        cursor: pointer;
    }
    .tos-link:hover {
        color: #2e5739;
    }

    /* Styling Kartu Persetujuan (Checklist) */
    .agreement-box {
        border: 2px solid #dee2e6;
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.3s ease;
        cursor: not-allowed;
        opacity: 0.6;
        display: flex;
        align-items: center;
    }
    .agreement-box.ready {
        border-color: var(--finvera-primary);
        background-color: white;
        cursor: pointer;
        opacity: 1;
        box-shadow: 0 4px 12px rgba(58, 109, 72, 0.1);
    }
    .agreement-box.ready:hover {
        background-color: #f4fcf6;
    }
    .custom-check-circle {
        width: 24px;
        height: 24px;
        border: 2px solid #adb5bd;
        border-radius: 50%;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .agreement-box.checked .custom-check-circle {
        background-color: var(--finvera-primary);
        border-color: var(--finvera-primary);
    }
    .agreement-box.checked {
        background-color: #e8f5e9;
        border-color: var(--finvera-primary);
    }
</style>

<div class="mb-4">
    <h3 class="fw-bold text-finvera">Lengkapi Data Diri</h3>
    <p class="text-muted">Data ini diperlukan untuk verifikasi akun dan credit scoring.</p>

    <div class="progress" style="height: 6px;">
        <div class="progress-bar bg-finvera" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="d-flex justify-content-between mt-2 small fw-bold">
        <span class="text-success"><i class="fas fa-check-circle"></i> Tahap 1: Akun</span>
        <span class="text-finvera">Tahap 2: Data Diri</span>
    </div>
</div>

<form action="{{ route('register.step2') }}" method="POST" id="regForm2">
    @csrf

    <!-- SECTION 1: PEKERJAAN & INCOME -->
    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-briefcase me-2 text-warning"></i>Pekerjaan & Penghasilan</h6>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Pekerjaan Saat Ini</label>
            <select name="job" id="jobSelect" class="form-select" required>
                <option value="">Pilih Pekerjaan...</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Lama Bekerja (Bulan)</label>
            <input type="number" name="employment_duration" class="form-control" placeholder="Contoh: 24" min="0" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Pendapatan Bulanan (Rupiah)</label>
        <div class="input-group mb-2">
            <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
            <input type="text" name="monthly_income_display" id="monthly_income_display" class="form-control border-start-0 fw-bold text-finvera fs-5" placeholder="0" required>
            <input type="hidden" name="monthly_income" id="monthly_income">
        </div>

        <div class="d-flex gap-2 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="3000000">3 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="5000000">5 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="10000000">10 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="15000000">15 Juta</button>
        </div>

        <label class="form-label small text-muted mb-1">Geser untuk menyesuaikan:</label>
        <input type="range" class="form-range" min="0" max="30000000" step="500000" id="income_slider">
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Tanggal Lahir</label>
        <input type="date" name="date_of_birth" class="form-control" required>
    </div>

    <!-- SECTION 2: ALAMAT LENGKAP (API) -->
    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="fas fa-map-marker-alt me-2 text-warning"></i>Alamat Domisili</h6>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Provinsi</label>
            <select name="province_select" id="province" class="form-select" required disabled>
                <option value="">Memuat...</option>
            </select>
            <input type="hidden" name="province" id="province_name">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Kota/Kabupaten</label>
            <select name="city_select" id="city" class="form-select" required disabled>
                <option value="">Pilih Provinsi Dulu</option>
            </select>
            <input type="hidden" name="city" id="city_name">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Kecamatan</label>
            <select name="district_select" id="district" class="form-select" required disabled>
                <option value="">Pilih Kota Dulu</option>
            </select>
            <input type="hidden" name="district" id="district_name">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Desa/Kelurahan</label>
            <select name="village_select" id="village" class="form-select" required disabled>
                <option value="">Pilih Kecamatan Dulu</option>
            </select>
            <input type="hidden" name="village" id="village_name">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Kode Pos</label>
        <input type="number" name="postal_code" id="postal_code" class="form-control" placeholder="Masukan Kode Pos Manual" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Alamat Lengkap (Jalan, No Rumah, RT/RW)</label>
        <textarea name="address_full" class="form-control" rows="2" placeholder="Contoh: Jl. Merdeka No. 45, RT 01/RW 02" required></textarea>
    </div>

    <!-- TOS SECTION -->
    <div class="card bg-light border-0 mt-4 shadow-sm">
        <div class="card-body">
            <div class="form-check ps-0">
                <input class="form-check-input ms-0 me-2" type="checkbox" name="tos_agreement" id="tosCheck" onclick="return false;" required style="float:none;">
                <label class="form-check-label small lh-sm" for="tosCheck" style="opacity: 1; vertical-align: middle;">
                    Saya menyetujui <a href="#" class="tos-link" data-bs-toggle="modal" data-bs-target="#tosModal">Syarat & Ketentuan Umum FinVera</a>.
                    <span class="text-danger">*</span>
                </label>
                <div class="form-text text-muted small mt-2 ms-1" id="tosHelp">
                    <i class="fas fa-hand-pointer me-1"></i> Klik tulisan hijau di atas untuk membaca.
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-finvera btn-lg shadow-sm fw-bold">
            Selesaikan Pendaftaran <i class="fas fa-check-circle ms-2"></i>
        </button>
    </div>
</form>

<!-- Modal TOS -->
<div class="modal fade" id="tosModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-finvera text-white">
                <h5 class="modal-title fw-bold fs-6"><i class="fas fa-file-contract me-2"></i>SYARAT & KETENTUAN UMUM</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-light" id="tosBody" style="font-family: 'Inter', sans-serif; line-height: 1.7; color: #495057;">
                <div class="bg-white p-4 rounded-3 shadow-sm mb-3">
                    <div class="text-center mb-4 border-bottom pb-3">
                        <i class="fas fa-leaf text-finvera fa-2x mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">FinVera - Aplikasi Pembiayaan Syariah</h6>
                        <small class="text-muted">Terakhir diperbarui: 2025</small>
                    </div>

                    <h6 class="fw-bold text-dark mt-3">1. Pendahuluan</h6>
                    <p class="small text-justify">Syarat dan Ketentuan Umum (“Ketentuan”) ini mengatur penggunaan aplikasi FinVera (“Aplikasi”), layanan pembiayaan syariah yang disediakan oleh PT FinVera Digital Indonesia (“Perusahaan”). Dengan mendaftar, mengakses, atau menggunakan Aplikasi, Pengguna menyatakan telah membaca, memahami, menyetujui, dan terikat oleh Ketentuan ini. FinVera menyediakan layanan pembiayaan syariah tanpa bunga, sesuai prinsip Syariah dan ketentuan Dewan Syariah Nasional (DSN-MUI), dengan pemanfaatan teknologi kecerdasan buatan (AI) untuk percepatan proses verifikasi dan penilaian pengajuan. Apabila Pengguna tidak menyetujui salah satu bagian dari Ketentuan ini, Pengguna diwajibkan untuk tidak menggunakan Aplikasi.</p>

                    <h6 class="fw-bold text-dark mt-3">2. Definisi</h6>
                    <p class="small text-justify">Dalam Ketentuan ini:</p>
                    <ul class="small text-justify ps-3">
                        <li>“Aplikasi”: Platform digital FinVera berbasis web/mobile yang menyediakan layanan pembiayaan syariah.</li>
                        <li>“Pengguna”: Individu yang membuat akun dan menggunakan layanan FinVera.</li>
                        <li>“Borrower”: Pengguna yang mengajukan pembiayaan.</li>
                        <li>“Perusahaan”: Pihak pengelola Aplikasi FinVera.</li>
                        <li>“Data Pribadi”: Setiap data, informasi, atau dokumen yang dapat mengidentifikasi individu.</li>
                        <li>“Pembiayaan Syariah”: Fasilitas pembiayaan yang dilakukan tanpa bunga (riba) dan sesuai ketentuan syariah (misal: Qardh, Murabahah, Ijarah, Mudharabah, Musyarakah).</li>
                        <li>“AI Verification”: Sistem otomatis untuk analisis data KYC, kelayakan pembiayaan, dan mitigasi risiko.</li>
                        <li>“KYC (Know Your Customer)”: Proses verifikasi identitas sesuai regulasi anti pencucian uang dan pencegahan pendanaan terorisme.</li>
                        <li>“Akad”: Perjanjian pembiayaan syariah yang mengikat Pengguna dan Perusahaan.</li>
                    </ul>

                    <!-- Bagian TOS lainnya disederhanakan untuk keterbacaan, tapi tetap mempertahankan struktur utama -->
                    <h6 class="fw-bold text-dark mt-3">3. Ruang Lingkup Layanan FinVera</h6>
                    <p class="small text-justify">Layanan FinVera mencakup: Pembuatan akun Pengguna, Verifikasi identitas dan KYC, Pengelolaan dan pengajuan pembiayaan syariah, Penilaian kelayakan pembiayaan berbasis AI, Pencairan dana pembiayaan sesuai akad, Penjadwalan dan pencatatan kewajiban pembayaran.</p>

                    <h6 class="fw-bold text-dark mt-3">15. Pernyataan Persetujuan</h6>
                    <p class="small text-justify">Dengan mencentang kotak persetujuan dan melanjutkan pendaftaran, Pengguna menyatakan: Telah membaca dan memahami isi Ketentuan, Menyetujui pemrosesan data pribadi, Menyetujui ketentuan pembiayaan syariah, Menyetujui penggunaan sistem AI dalam proses penilaian, Bersedia mematuhi seluruh kebijakan FinVera.</p>
                </div>

                <div class="alert alert-warning mt-4 text-center fw-bold shadow-sm py-2 small" id="scrollAlert">
                    <i class="fas fa-arrow-down me-2 fa-bounce"></i> Gulir sampai bawah untuk menyetujui
                </div>
            </div>

            <div class="modal-footer bg-white border-top shadow-lg pt-3 pb-3" style="z-index: 10;">
                <div class="w-100">
                    <div class="agreement-box" id="agreementBox">
                        <div class="custom-check-circle" id="checkIcon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Saya Setuju dengan Syarat & Ketentuan</h6>
                            <small class="text-muted lh-1" id="agreementSubtext">Baca sampai akhir untuk mengaktifkan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // --- INIT SELECT2 ---
        $('#jobSelect, #province, #city, #district, #village').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Silakan pilih...'
        });

        // --- 1. JOB LIST ---
        const jobs = [
            "PNS/ASN", "TNI/Polri", "Pegawai BUMN", "Karyawan Swasta", "Wiraswasta/Pengusaha",
            "Dokter", "Guru/Dosen", "Perawat/Bidan", "Pengacara/Notaris", "Akuntan",
            "Programmer/IT", "Freelancer", "Pedagang", "Petani/Nelayan", "Supir/Driver Online",
            "Ibu Rumah Tangga", "Pelajar/Mahasiswa", "Buruh Pabrik", "Teknisi", "Lainnya"
        ];
        jobs.forEach(job => $('#jobSelect').append(new Option(job, job)));

        // --- 2. INCOME FORMATTER LOGIC ---
        const $display = $('#monthly_income_display');
        const $hidden = $('#monthly_income');
        const $slider = $('#income_slider');

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        function unformatNumber(str) {
            return str.replace(/\./g, '');
        }

        $slider.on('input', function() {
            let val = $(this).val();
            $hidden.val(val);
            $display.val(formatNumber(val));
        });

        $display.on('input', function() {
            let rawVal = unformatNumber($(this).val());
            let val = parseInt(rawVal) || 0;
            $hidden.val(val);
            $slider.val(val);
            $(this).val(formatNumber(rawVal));
        });

        $('.income-chip').click(function() {
            let val = $(this).data('val');
            $hidden.val(val);
            $slider.val(val);
            $display.val(formatNumber(val));
        });

        // --- 3. REGIONAL API (EMSIFA) ---
        const apiBaseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        function updateHiddenInput(selector, hiddenSelector) {
            let text = $(selector).find('option:selected').text();
            $(hiddenSelector).val(text);
        }

        // Load Provinces
        $.get(`${apiBaseUrl}/provinces.json`, function(data) {
            let $prov = $('#province');
            $prov.empty().append('<option value="">Pilih Provinsi</option>').prop('disabled', false);
            data.forEach(p => $prov.append(new Option(p.name, p.id, false, false)));
            $prov.trigger('change.select2');
        });

        // Province Change
        $('#province').on('change', function() {
            let provId = $(this).val();
            updateHiddenInput(this, '#province_name');

            $('#city').empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change.select2');
            $('#district').empty().append('<option value="">Pilih Kota Dulu</option>').prop('disabled', true).trigger('change.select2');
            $('#village').empty().append('<option value="">Pilih Kecamatan Dulu</option>').prop('disabled', true).trigger('change.select2');

            if(provId) {
                $.get(`${apiBaseUrl}/regencies/${provId}.json`, function(data) {
                    let $city = $('#city');
                    $city.empty().append('<option value="">Pilih Kota/Kab</option>').prop('disabled', false);
                    data.forEach(d => $city.append(new Option(d.name, d.id, false, false)));
                    $city.trigger('change.select2');
                });
            }
        });

        // City Change
        $('#city').on('change', function() {
            let cityId = $(this).val();
            updateHiddenInput(this, '#city_name');

            $('#district').empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change.select2');

            if(cityId) {
                $.get(`${apiBaseUrl}/districts/${cityId}.json`, function(data) {
                    let $dist = $('#district');
                    $dist.empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', false);
                    data.forEach(d => $dist.append(new Option(d.name, d.id, false, false)));
                    $dist.trigger('change.select2');
                });
            }
        });

        // District Change
        $('#district').on('change', function() {
            let distId = $(this).val();
            updateHiddenInput(this, '#district_name');

            $('#village').empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change.select2');

            if(distId) {
                $.get(`${apiBaseUrl}/villages/${distId}.json`, function(data) {
                    let $vill = $('#village');
                    $vill.empty().append('<option value="">Pilih Desa</option>').prop('disabled', false);
                    data.forEach(d => $vill.append(new Option(d.name, d.name, false, false)));
                    $vill.trigger('change.select2');
                });
            }
        });

        // Village Change
        $('#village').on('change', function() {
            updateHiddenInput(this, '#village_name');
        });

        // --- 4. TOS SCROLL & VALIDATION LOGIC ---
        const tosBody = document.getElementById('tosBody');
        const agreementBox = document.getElementById('agreementBox');
        const agreementSubtext = document.getElementById('agreementSubtext');
        const scrollAlert = document.getElementById('scrollAlert');
        const mainCheck = document.getElementById('tosCheck');
        const tosModal = new bootstrap.Modal(document.getElementById('tosModal'));
        let isRead = false;

        tosBody.addEventListener('scroll', function() {
            if (this.scrollTop + this.clientHeight >= this.scrollHeight - 20) {
                if (!isRead) {
                    isRead = true;
                    agreementBox.classList.add('ready');
                    agreementBox.style.cursor = 'pointer';
                    agreementSubtext.innerText = "Klik di sini untuk menyetujui.";
                    agreementSubtext.classList.add('text-success', 'fw-bold');
                    agreementSubtext.classList.remove('text-muted');
                    scrollAlert.className = "alert alert-success text-center fw-bold shadow-sm py-2 small";
                    scrollAlert.innerHTML = '<i class="fas fa-check-circle me-1"></i> Terima kasih sudah membaca.';
                }
            }
        });

        agreementBox.addEventListener('click', function() {
            if (isRead) {
                this.classList.add('checked');
                mainCheck.checked = true;
                const helpText = document.getElementById('tosHelp');
                helpText.innerHTML = '<i class="fas fa-check-circle me-1"></i> Anda telah menyetujui S&K.';
                helpText.classList.remove('text-muted');
                helpText.classList.add('text-success', 'fw-bold');
                setTimeout(() => { tosModal.hide(); }, 300);
            }
        });

        $('.tos-link').click(function(e) {
            e.preventDefault();
            tosModal.show();
        });

        // --- 5. INTERCEPT LOGIN LINK (Guard) ---
        $('a[href*="login"]').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Selesaikan Pendaftaran',
                text: 'Mohon selesaikan pengisian data diri Anda (Tahap 2) terlebih dahulu.',
                confirmButtonColor: '#3A6D48',
                confirmButtonText: 'Mengerti'
            });
        });
    });
</script>
@endpush
@endsection
