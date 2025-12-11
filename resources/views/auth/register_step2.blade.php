@extends('layouts.auth')

@section('title', 'Lengkapi Data Diri')

@section('content')
<style>
    /* Custom Style Select2 */
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
                <!-- Opsi via JS -->
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
            <span class="input-group-text">Rp</span>
            <!-- Input Display (Format Rupiah) -->
            <input type="text" name="monthly_income_display" id="monthly_income_display" class="form-control fw-bold text-finvera" placeholder="0" required>
            <!-- Input Hidden (Nilai Asli ke Backend) -->
            <input type="hidden" name="monthly_income" id="monthly_income">
        </div>

        <div class="d-flex gap-2 flex-wrap mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="3000000">3 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="5000000">5 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="10000000">10 Juta</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill income-chip" data-val="15000000">15 Juta</button>
        </div>
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
            <select name="province" id="province" class="form-select" required disabled>
                <option value="">Memuat...</option>
            </select>
            <input type="hidden" name="province_name" id="province_name">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Kota/Kabupaten</label>
            <select name="city" id="city" class="form-select" required disabled>
                <option value="">Pilih Provinsi Dulu</option>
            </select>
            <input type="hidden" name="city_name" id="city_name">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Kecamatan</label>
            <select name="district" id="district" class="form-select" required disabled>
                <option value="">Pilih Kota Dulu</option>
            </select>
            <input type="hidden" name="district_name" id="district_name">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold text-muted">Desa/Kelurahan</label>
            <select name="village" id="village" class="form-select" required disabled>
                <option value="">Pilih Kecamatan Dulu</option>
            </select>
            <input type="hidden" name="village_name" id="village_name">
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
    <div class="card bg-light border-0 mt-4">
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tos_agreement" id="tosCheck" disabled required>
                <label class="form-check-label small" for="tosCheck">
                    Saya menyetujui <a href="#" class="text-decoration-none fw-bold text-finvera" data-bs-toggle="modal" data-bs-target="#tosModal">Syarat & Ketentuan Umum FinVera</a>.
                    <span class="text-danger">*</span>
                </label>
                <div class="form-text text-muted small" id="tosHelp">Anda wajib membaca S&K hingga akhir untuk menyetujui.</div>
            </div>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-finvera btn-lg shadow-sm">
            Selesaikan Pendaftaran <i class="fas fa-check-circle ms-2"></i>
        </button>
    </div>
</form>

<!-- Modal TOS -->
<div class="modal fade" id="tosModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-finvera text-white">
                <h5 class="modal-title fw-bold fs-6">SYARAT & KETENTUAN UMUM FINVERA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small" id="tosBody">
                <h6 class="fw-bold text-center">FinVera - Aplikasi Pembiayaan Syariah Berbasis Teknologi</h6>
                <p class="text-center text-muted mb-4">Terakhir diperbarui: 2025</p>

                <p><strong>1. Pendahuluan</strong><br>
                Syarat dan Ketentuan Umum (“Ketentuan”) ini mengatur penggunaan aplikasi FinVera (“Aplikasi”), layanan pembiayaan syariah yang disediakan oleh PT FinVera Digital Indonesia (“Perusahaan”). Dengan mendaftar, mengakses, atau menggunakan Aplikasi, Pengguna menyatakan telah membaca, memahami, menyetujui, dan terikat oleh Ketentuan ini. FinVera menyediakan layanan pembiayaan syariah tanpa bunga, sesuai prinsip Syariah dan ketentuan Dewan Syariah Nasional (DSN-MUI), dengan pemanfaatan teknologi kecerdasan buatan (AI) untuk percepatan proses verifikasi dan penilaian pengajuan. Apabila Pengguna tidak menyetujui salah satu bagian dari Ketentuan ini, Pengguna diwajibkan untuk tidak menggunakan Aplikasi.</p>

                <p><strong>2. Definisi</strong><br>
                Dalam Ketentuan ini:<br>
                “Aplikasi”: Platform digital FinVera berbasis web/mobile yang menyediakan layanan pembiayaan syariah.<br>
                “Pengguna”: Individu yang membuat akun dan menggunakan layanan FinVera.<br>
                “Borrower”: Pengguna yang mengajukan pembiayaan.<br>
                “Perusahaan”: Pihak pengelola Aplikasi FinVera.<br>
                “Data Pribadi”: Setiap data, informasi, atau dokumen yang dapat mengidentifikasi individu.<br>
                “Pembiayaan Syariah”: Fasilitas pembiayaan yang dilakukan tanpa bunga (riba) dan sesuai ketentuan syariah (misal: Qardh, Murabahah, Ijarah, Mudharabah, Musyarakah).<br>
                “AI Verification”: Sistem otomatis untuk analisis data KYC, kelayakan pembiayaan, dan mitigasi risiko.<br>
                “KYC (Know Your Customer)”: Proses verifikasi identitas sesuai regulasi anti pencucian uang dan pencegahan pendanaan terorisme.<br>
                “Akad”: Perjanjian pembiayaan syariah yang mengikat Pengguna dan Perusahaan.</p>

                <p><strong>3. Ruang Lingkup Layanan FinVera</strong><br>
                Layanan FinVera mencakup: Pembuatan akun Pengguna, Verifikasi identitas dan KYC, Pengelolaan dan pengajuan pembiayaan syariah, Penilaian kelayakan pembiayaan berbasis AI, Pencairan dana pembiayaan sesuai akad, Penjadwalan dan pencatatan kewajiban pembayaran, Notifikasi digital terkait status pembiayaan, Penyimpanan riwayat transaksi dan dokumen akad. FinVera tidak memberikan layanan yang bertentangan dengan prinsip Syariah, termasuk: bunga, denda keterlambatan berbasis persentase, penalti merugikan, transaksi spekulatif/gharar.</p>

                <p><strong>4. Ketentuan Akun Pengguna</strong><br>
                Pengguna wajib mendaftarkan akun menggunakan data yang benar, lengkap, dan dapat dipertanggungjawabkan. Pengguna wajib berusia minimal 18 tahun dan memiliki KTP yang masih berlaku. Pengguna bertanggung jawab penuh atas keamanan akun, termasuk kata sandi dan akses perangkat. Perusahaan berhak menangguhkan atau menutup akun apabila ditemukan: pelanggaran ketentuan, ketidaksesuaian data, indikasi penipuan atau penyalahgunaan. Setiap perubahan data pribadi wajib diperbarui oleh Pengguna melalui Aplikasi.</p>

                <p><strong>5. Verifikasi Identitas dan KYC</strong><br>
                Pengguna wajib melalui proses KYC sebelum mengakses layanan pembiayaan. FinVera menggunakan kombinasi verifikasi: unggah dokumen identitas, selfie biometrik, verifikasi NIK, pengecekan keaslian dokumen, validasi otomatis berbasis AI. Pengguna menyetujui bahwa data KYC disimpan dan diproses sesuai ketentuan perlindungan data pribadi. Perusahaan berhak meminta verifikasi tambahan apabila diperlukan. Pengguna menjamin bahwa seluruh dokumen yang diberikan adalah benar dan tidak dipalsukan.</p>

                <p><strong>6. Penggunaan Data Pribadi</strong><br>
                FinVera mengumpulkan dan memproses Data Pribadi untuk: proses KYC, analisis kelayakan pembiayaan, penyusunan akad, pencairan dana, pengelolaan pembayaran, kepatuhan terhadap regulasi. Data Pengguna disimpan dengan standar keamanan tinggi dan tidak diperjualbelikan. FinVera dapat membagikan data Pengguna kepada pihak yang bekerja sama dalam pengolahan pembiayaan, sepanjang sesuai syariah dan hukum. Pengguna berhak meminta penghapusan data sesuai prosedur legal yang berlaku.</p>

                <p><strong>7. Ketentuan Pengajuan Pembiayaan</strong><br>
                Pengguna dapat mengajukan pembiayaan setelah lulus verifikasi KYC dan verifikasi rekening bank. Jenis pembiayaan dapat berbeda tergantung produk (misal Qardh atau Murabahah). Dengan mengajukan pembiayaan, Pengguna menyatakan memahami dan menyetujui: detail produk pembiayaan, biaya administrasi (jika ada), jadwal pembayaran, perhitungan margin (untuk Murabahah), konsekuensi wanprestasi. Keputusan persetujuan pembiayaan dilakukan melalui sistem AI dan/atau verifikasi manual. Perusahaan berhak menolak pengajuan tanpa kewajiban memberikan alasan detail.</p>

                <p><strong>8. Pencairan Dana</strong><br>
                Dana akan dicairkan ke rekening bank yang divalidasi Pengguna. Waktu pencairan umumnya <24 jam setelah akad disetujui, kecuali terjadi kendala sistem, bank, atau verifikasi lanjutan. FinVera tidak bertanggung jawab atas keterlambatan dari pihak bank atau force majeure.</p>

                <p><strong>9. Kewajiban Pembayaran</strong><br>
                Pengguna wajib membayar kewajiban sesuai jadwal yang tercantum di Aplikasi. FinVera tidak mengenakan bunga dalam bentuk apa pun. Untuk akad yang mengizinkan margin (misal Murabahah), margin ditentukan di awal dan disepakati dalam akad. Apabila terjadi keterlambatan pembayaran: Pengguna tetap wajib melunasi pokok/margin sesuai akad, FinVera tidak mengenakan denda bunga, Kontribusi keterlambatan (ta’zir) tidak bersifat komersial dan sepenuhnya dialokasikan untuk dana sosial (jika diterapkan), FinVera dapat memberikan pengingat, penjadwalan ulang, atau rescheduling.</p>

                <p><strong>10. Larangan Penggunaan</strong><br>
                Pengguna dilarang menggunakan Aplikasi untuk: Pengajuan dengan identitas palsu, Pemalsuan dokumen, Pencucian uang atau pendanaan terorisme, Penyalahgunaan dana pembiayaan, Upaya manipulasi sistem AI atau keamanan Aplikasi, Aktivitas lain yang bertentangan dengan hukum dan prinsip syariah. Pelanggaran dapat menyebabkan penutupan akun, pembatalan pembiayaan, atau tindakan hukum.</p>

                <p><strong>11. Hak & Kewajiban Perusahaan</strong><br>
                Perusahaan berhak: Menolak permohonan pembiayaan, Melakukan pembekuan akun, Melakukan evaluasi tambahan, Mengubah atau menghentikan layanan, Menerapkan kebijakan keamanan internal. Perusahaan berkewajiban untuk: Menjaga kerahasiaan data Pengguna, Mengoperasikan layanan sesuai prinsip syariah, Menyediakan informasi produk secara jujur dan transparan, Menyelesaikan keluhan Pengguna secara profesional.</p>

                <p><strong>12. Pembaruan Ketentuan</strong><br>
                Perusahaan dapat memperbarui Ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui Aplikasi. Penggunaan berkelanjutan dianggap sebagai persetujuan terhadap Ketentuan yang diperbarui.</p>

                <p><strong>13. Batas Tanggung Jawab</strong><br>
                Perusahaan tidak bertanggung jawab atas: Gangguan layanan akibat force majeure, Akses ilegal oleh pihak ketiga, Kesalahan input data oleh Pengguna, Keterlambatan transaksi bank, Kerugian yang timbul akibat pelanggaran Pengguna sendiri.</p>

                <p><strong>14. Penyelesaian Sengketa</strong><br>
                Setiap sengketa akan diselesaikan melalui: Musyawarah dan mediasi internal, Media penyelesaian alternatif sengketa syariah, Badan Arbitrase Syariah (jika disepakati), Lembaga hukum sesuai yurisdiksi Indonesia.</p>

                <p><strong>15. Pernyataan Persetujuan</strong><br>
                Dengan mencentang kotak persetujuan dan melanjutkan pendaftaran, Pengguna menyatakan: Telah membaca dan memahami isi Ketentuan, Menyetujui pemrosesan data pribadi, Menyetujui ketentuan pembiayaan syariah, Menyetujui penggunaan sistem AI dalam proses penilaian, Bersedia mematuhi seluruh kebijakan FinVera.</p>

                <div class="alert alert-warning mt-4 text-center" id="scrollAlert">
                    <i class="fas fa-arrow-down me-2"></i> Silakan gulir sampai bawah untuk menyetujui
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="form-check me-auto">
                    <input class="form-check-input" type="checkbox" id="modalTosCheck" disabled>
                    <label class="form-check-label fw-bold" for="modalTosCheck">
                        Saya menyetujui Syarat & Ketentuan di atas
                    </label>
                </div>
                <button type="button" class="btn btn-primary" id="btnAcceptTos" disabled data-bs-dismiss="modal">Simpan & Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // --- INIT SELECT2 UNTUK SEMUA DROPDOWN ---
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

        // Format: 5,000,000
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Unformat: 5000000
        function unformatNumber(str) {
            return str.replace(/,/g, '');
        }

        // Update Hidden & Slider
        function updateValues(val) {
            val = parseInt(val) || 0;
            $hidden.val(val);
            $slider.val(val);

            if (!$display.is(":focus")) {
                $display.val(formatNumber(val));
            } else {
                $display.val(val);
            }
        }

        // Event: Slider Change
        $slider.on('input', function() {
            let val = $(this).val();
            $hidden.val(val);
            $display.val(formatNumber(val));
        });

        // Event: Input Manual Change
        $display.on('input', function() {
            let rawVal = unformatNumber($(this).val());
            let val = parseInt(rawVal) || 0;
            $hidden.val(val);
            $slider.val(val);
        });

        // Event: Focus (Clean for edit)
        $display.on('focus', function() {
            let val = $hidden.val();
            if(val == 0) val = '';
            $(this).val(val);
        });

        // Event: Blur (Format result)
        $display.on('blur', function() {
            let val = $hidden.val();
            $(this).val(formatNumber(val));
        });

        // Event: Chip Shortcuts
        $('.income-chip').click(function() {
            let val = $(this).data('val');
            $hidden.val(val);
            $slider.val(val);
            $display.val(formatNumber(val));
        });


        // --- 3. REGIONAL API (EMSIFA) WITH SELECT2 ---
        const apiBaseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        // Load Provinces
        $.get(`${apiBaseUrl}/provinces.json`, function(data) {
            let $prov = $('#province');
            $prov.empty().append('<option value="">Pilih Provinsi</option>').prop('disabled', false);
            data.forEach(p => $prov.append(new Option(p.name, p.id, false, false)));
            $prov.trigger('change.select2');
        });

        // Province -> Load City
        $('#province').on('change', function() {
            let provId = $(this).val();
            let provName = $(this).find('option:selected').text();

            $(this).attr('name', 'province_id');
            $('input[name="province"]').remove();
            $('<input>').attr({type: 'hidden', name: 'province', value: provName}).appendTo('form');

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

        // City -> Load District
        $('#city').on('change', function() {
            let cityId = $(this).val();
            let cityName = $(this).find('option:selected').text();
            $(this).attr('name', 'city_id');
            $('input[name="city"]').remove();
            $('<input>').attr({type: 'hidden', name: 'city', value: cityName}).appendTo('form');

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

        // District -> Load Village
        $('#district').on('change', function() {
            let distId = $(this).val();
            let distName = $(this).find('option:selected').text();
            $(this).attr('name', 'district_id');
            $('input[name="district"]').remove();
            $('<input>').attr({type: 'hidden', name: 'district', value: distName}).appendTo('form');

            $('#village').empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change.select2');

            if(distId) {
                $.get(`${apiBaseUrl}/villages/${distId}.json`, function(data) {
                    let $vill = $('#village');
                    $vill.empty().append('<option value="">Pilih Desa</option>').prop('disabled', false);
                    data.forEach(d => $vill.append(new Option(d.name, d.id, false, false)));
                    $vill.trigger('change.select2');
                });
            }
        });

        // Village Change
        $('#village').on('change', function() {
            let villName = $(this).find('option:selected').text();
            $(this).attr('name', 'village_id');
            $('input[name="village"]').remove();
            $('<input>').attr({type: 'hidden', name: 'village', value: villName}).appendTo('form');
        });

        // --- 4. TOS SCROLL LOGIC ---
        const tosBody = document.getElementById('tosBody');
        const modalCheck = document.getElementById('modalTosCheck');
        const acceptBtn = document.getElementById('btnAcceptTos');
        const mainCheck = document.getElementById('tosCheck');
        const scrollAlert = document.getElementById('scrollAlert');

        tosBody.addEventListener('scroll', function() {
            if (this.scrollTop + this.clientHeight >= this.scrollHeight - 5) {
                modalCheck.disabled = false;
                scrollAlert.className = "alert alert-success mt-4 text-center";
                scrollAlert.innerHTML = '<i class="fas fa-check me-2"></i> Terima kasih sudah membaca.';
            }
        });

        modalCheck.addEventListener('change', function() {
            acceptBtn.disabled = !this.checked;
        });

        acceptBtn.addEventListener('click', function() {
            if (!modalCheck.disabled && modalCheck.checked) {
                mainCheck.checked = true;
                mainCheck.disabled = false;
                document.getElementById('tosHelp').innerText = "Terima kasih telah menyetujui S&K.";
                document.getElementById('tosHelp').classList.add('text-success');
                document.getElementById('tosHelp').classList.remove('text-muted');
            }
        });

        mainCheck.addEventListener('click', function(e) {
            if(!modalCheck.checked) {
                e.preventDefault();
                var myModal = new bootstrap.Modal(document.getElementById('tosModal'));
                myModal.show();
            }
        });
    });
</script>
@endpush
@endsection
