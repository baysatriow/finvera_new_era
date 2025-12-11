@extends('layouts.dashboard')

@section('page_title', 'Profil Pengguna')

@section('content')
<!-- Load CSS Select2 -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Styling Avatar */
    .profile-avatar {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        margin: 0 auto 1.5rem auto;
    }

    /* Fix Select2 Height */
    .select2-container .select2-selection--single {
        height: 48px !important;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #212529;
        font-size: 0.95rem;
    }
    /* Input Height Consistency */
    .form-control, .form-select {
        height: 48px;
        border-radius: 8px;
    }
    textarea.form-control { height: auto; }
</style>
@endpush

<div class="row">
    <!-- KOLOM KIRI: INFO SINGKAT -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
            <!-- Avatar -->
            <div class="profile-avatar {{ Auth::user()->role == 'admin' ? 'bg-danger text-danger bg-opacity-10' : 'bg-finvera text-white' }} rounded-circle shadow-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>

            <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
            <p class="text-muted small mb-4">{{ Auth::user()->email }}</p>

            <div class="d-flex justify-content-center gap-2 mb-4">
                @if(Auth::user()->role == 'admin')
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                        <i class="fas fa-user-shield me-1"></i> Administrator
                    </span>
                @else
                    @if(Auth::user()->kyc_status == 'verified')
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> Terverifikasi</span>
                    @elseif(Auth::user()->kyc_status == 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i> Menunggu</span>
                    @else
                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fas fa-times-circle me-1"></i> Belum KYC</span>
                    @endif
                @endif
            </div>

            <hr class="border-light mb-4">

            <!-- Statistik Kredit (HANYA UNTUK BORROWER) -->
            @if(Auth::user()->role == 'borrower')
            <div class="text-start px-2">
                <label class="small text-muted fw-bold text-uppercase mb-2">Statistik Kredit</label>
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <div>
                        <small class="d-block text-muted">AI Score</small>
                        <span class="fw-bold fs-5 {{ Auth::user()->credit_score >= 70 ? 'text-success' : 'text-danger' }}">
                            {{ Auth::user()->credit_score > 0 ? Auth::user()->credit_score : '-' }}
                        </span>
                    </div>
                    <div class="text-end">
                        <small class="d-block text-muted">Status</small>
                        <span class="fw-bold text-dark">
                            {{ Auth::user()->credit_score >= 70 ? 'Layak' : (Auth::user()->credit_score > 0 ? 'Berisiko' : 'Belum Ada') }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- KOLOM KANAN: FORM EDIT -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-finvera" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button">
                            <i class="fas fa-user-edit me-2"></i>Edit Profil
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-muted" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                            <i class="fas fa-lock me-2"></i>Ganti Password
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="profileTabsContent">

                    <!-- TAB 1: EDIT PROFIL -->
                    <div class="tab-pane fade show active" id="edit" role="tabpanel">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-id-card me-2 text-warning"></i>Data Diri Utama</h6>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Email (Terkunci)</label>
                                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                                    <!-- Email tidak dikirim -->
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small fw-bold text-muted">Nomor HP</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Tanggal Lahir</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <!-- Bagian Pekerjaan HANYA UNTUK BORROWER -->
                            @if(Auth::user()->role == 'borrower')
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="fas fa-briefcase me-2 text-warning"></i>Pekerjaan & Keuangan</h6>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small fw-bold text-muted">Pekerjaan</label>
                                    <select name="job" id="jobSelect" class="form-select" required>
                                        <option value="{{ $user->job }}" selected>{{ $user->job }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Lama Bekerja (Bulan)</label>
                                    <input type="number" name="employment_duration" class="form-control" value="{{ old('employment_duration', $user->employment_duration) }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Pendapatan Bulanan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" id="monthly_income_display" class="form-control fw-bold text-finvera" required>
                                    <input type="hidden" name="monthly_income" id="monthly_income" value="{{ old('monthly_income', $user->monthly_income) }}">
                                </div>
                            </div>
                            @endif

                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="fas fa-map-marker-alt me-2 text-warning"></i>Alamat Domisili</h6>

                            <div class="alert alert-info small py-2 border-0 shadow-sm mb-3">
                                <i class="fas fa-info-circle me-1"></i> Pilih ulang Provinsi jika ingin mengubah alamat lengkap.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small fw-bold text-muted">Provinsi</label>
                                    <!-- Name diubah jadi province_select agar tidak terkirim sebagai 'province' -->
                                    <select name="province_select" id="province" class="form-select" required>
                                        <option value="{{ $user->province }}" selected>{{ $user->province }}</option>
                                    </select>
                                    <!-- Input Hidden 'province' ini yang akan dikirim ke DB dengan Nama Wilayah -->
                                    <input type="hidden" name="province" id="province_hidden" value="{{ $user->province }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Kota/Kabupaten</label>
                                    <select name="city_select" id="city" class="form-select" required>
                                        <option value="{{ $user->city }}" selected>{{ $user->city }}</option>
                                    </select>
                                    <input type="hidden" name="city" id="city_hidden" value="{{ $user->city }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small fw-bold text-muted">Kecamatan</label>
                                    <select name="district_select" id="district" class="form-select" required>
                                        <option value="{{ $user->district }}" selected>{{ $user->district }}</option>
                                    </select>
                                    <input type="hidden" name="district" id="district_hidden" value="{{ $user->district }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Desa/Kelurahan</label>
                                    <select name="village_select" id="village" class="form-select" required>
                                        <option value="{{ $user->village }}" selected>{{ $user->village }}</option>
                                    </select>
                                    <input type="hidden" name="village" id="village_hidden" value="{{ $user->village }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Kode Pos</label>
                                <input type="number" name="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code) }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Alamat Lengkap</label>
                                <textarea name="address_full" class="form-control" rows="2" required>{{ old('address_full', $user->address_full) }}</textarea>
                            </div>

                            <div class="d-grid d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-finvera px-5 fw-bold shadow-sm">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: GANTI PASSWORD -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                <i class="fas fa-shield-alt me-2"></i> Password minimal 8 karakter.
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password Baru</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="d-grid d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-warning text-white px-5 fw-bold shadow-sm">
                                    <i class="fas fa-key me-2"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // --- 1. INIT SELECT2 ---
    $('#jobSelect, #province, #city, #district, #village').select2({
        theme: 'bootstrap-5',
        width: '100%',
    });

    // Populate Jobs & Income Formatter (Hanya jika elemen ada/User=Borrower)
    if($('#jobSelect').length) {
        const jobs = [
            "PNS/ASN", "TNI/Polri", "Pegawai BUMN", "Karyawan Swasta", "Wiraswasta/Pengusaha",
            "Dokter", "Guru/Dosen", "Perawat/Bidan", "Pengacara/Notaris", "Akuntan",
            "Programmer/IT", "Freelancer", "Pedagang", "Petani/Nelayan", "Supir/Driver Online",
            "Ibu Rumah Tangga", "Pelajar/Mahasiswa", "Buruh Pabrik", "Teknisi", "Lainnya"
        ];
        const currentUserJob = "{{ $user->job }}";
        jobs.forEach(job => {
            if (job !== currentUserJob) {
                $('#jobSelect').append(new Option(job, job));
            }
        });

        // Income Formatter
        const $display = $('#monthly_income_display');
        const $hidden = $('#monthly_income');

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        function unformatNumber(str) {
            return str.replace(/,/g, '');
        }

        if ($hidden.val()) {
            $display.val(formatNumber($hidden.val()));
        }

        $display.on('input', function() {
            let rawVal = unformatNumber($(this).val());
            let val = parseInt(rawVal) || 0;
            $hidden.val(val);
            $(this).val(formatNumber(rawVal));
        });
    }

    // --- 3. REGIONAL API (EMSIFA) ---
    const apiBaseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    // Load Provinces
    $.get(`${apiBaseUrl}/provinces.json`, function(data) {
        let $prov = $('#province');
        // Loop data provinsi dari API
        data.forEach(p => {
            // Cek jika nama provinsi di DB sama dengan API
            // Jika sama, kita ubah value option yang sudah ada (dari HTML) menjadi ID agar API child bisa jalan
            if (p.name === "{{ $user->province }}") {
                // Update value option yang 'selected' saat ini menjadi ID API
                $prov.find('option:selected').val(p.id);
            } else {
                // Jika beda, tambahkan sebagai opsi baru
                $prov.append(new Option(p.name, p.id, false, false));
            }
        });
    });

    // Helper untuk update input hidden dengan NAMA Wilayah (Text)
    function updateHiddenInput(selector, hiddenSelector) {
        let text = $(selector).find('option:selected').text();
        $(hiddenSelector).val(text);
    }

    // PROVINCE CHANGE
    $('#province').on('change', function() {
        let id = $(this).val();
        updateHiddenInput(this, '#province_hidden'); // Simpan NAMA ke hidden input

        // Reset Child Selects
        $('#city').empty().append('<option value="">Pilih Kota</option>').trigger('change');
        $('#district').empty().append('<option value="">Pilih Kecamatan</option>').trigger('change');
        $('#village').empty().append('<option value="">Pilih Desa</option>').trigger('change');

        // Fetch Cities
        if(id && !isNaN(id)) {
            $.get(`${apiBaseUrl}/regencies/${id}.json`, function(data) {
                data.forEach(d => $('#city').append(new Option(d.name, d.id)));
            });
        }
    });

    // CITY CHANGE
    $('#city').on('change', function() {
        let id = $(this).val();
        updateHiddenInput(this, '#city_hidden');

        $('#district').empty().append('<option value="">Pilih Kecamatan</option>').trigger('change');
        $('#village').empty().append('<option value="">Pilih Desa</option>').trigger('change');

        if(id && !isNaN(id)) {
            $.get(`${apiBaseUrl}/districts/${id}.json`, function(data) {
                data.forEach(d => $('#district').append(new Option(d.name, d.id)));
            });
        }
    });

    // DISTRICT CHANGE
    $('#district').on('change', function() {
        let id = $(this).val();
        updateHiddenInput(this, '#district_hidden');

        $('#village').empty().append('<option value="">Pilih Desa</option>').trigger('change');

        if(id && !isNaN(id)) {
            $.get(`${apiBaseUrl}/villages/${id}.json`, function(data) {
                // Khusus Desa, value di option juga nama (untuk simplifikasi, atau id juga boleh asal konsisten)
                // Disini kita pakai nama sebagai value dan text agar seragam
                data.forEach(d => $('#village').append(new Option(d.name, d.name)));
            });
        }
    });

    // VILLAGE CHANGE
    $('#village').on('change', function() {
        updateHiddenInput(this, '#village_hidden');
    });
});
</script>
@endpush
@endsection
