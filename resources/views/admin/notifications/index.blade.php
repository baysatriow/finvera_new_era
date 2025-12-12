@extends('layouts.dashboard')

@section('page_title', 'Kelola Notifikasi')

@section('content')

@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .type-radio-group input[type="radio"] {
        display: none;
    }
    .type-radio-group label {
        display: block;
        padding: 15px;
        border: 2px solid #eee;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        opacity: 0.7;
    }
    .type-radio-group input[type="radio"]:checked + label {
        opacity: 1;
        border-color: currentColor;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
        font-weight: bold;
    }
    .type-info { color: #0d6efd; }
    .type-success { color: #198754; }
    .type-warning { color: #ffc107; }
    .type-danger { color: #dc3545; }

    .type-radio-group input[type="radio"]:checked + label.lbl-info { background-color: #f0f7ff; border-color: #0d6efd; }
    .type-radio-group input[type="radio"]:checked + label.lbl-success { background-color: #f0fff4; border-color: #198754; }
    .type-radio-group input[type="radio"]:checked + label.lbl-warning { background-color: #fffbf0; border-color: #ffc107; }
    .type-radio-group input[type="radio"]:checked + label.lbl-danger { background-color: #fff0f0; border-color: #dc3545; }

    .table-check { width: 40px; text-align: center; }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 6px 15px;
        border: 1px solid #dee2e6;
    }
    .pointer-events-none { pointer-events: none; }
</style>
@endpush

<form action="{{ route('admin.notifications.send') }}" method="POST" id="notifForm">
    @csrf

    <div class="row g-4">

        <!-- FITUR: KONTEN NOTIFIKASI -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-4 px-4 border-bottom-0">
                    <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-edit me-2 text-finvera"></i>Konten Notifikasi</h5>
                    <p class="text-muted small mb-0">Tentukan isi pesan yang akan dikirim.</p>
                </div>
                <div class="card-body p-4 pt-0">

                    <!-- SUB-FITUR: JUDUL -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Judul Notifikasi</label>
                        <input type="text" name="title" class="form-control py-3 rounded-3" placeholder="Contoh: Pemeliharaan Sistem" required>
                    </div>

                    <!-- SUB-FITUR: TIPE PESAN (LEVEL) -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">Tipe / Level Pesan</label>
                        <div class="row g-2 type-radio-group">
                            <div class="col-6">
                                <input type="radio" name="type" id="typeInfo" value="info" checked>
                                <label for="typeInfo" class="lbl-info type-info">
                                    <i class="fas fa-info-circle fa-lg mb-2 d-block"></i> Info
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="type" id="typeSuccess" value="success">
                                <label for="typeSuccess" class="lbl-success type-success">
                                    <i class="fas fa-check-circle fa-lg mb-2 d-block"></i> Sukses
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="type" id="typeWarning" value="warning">
                                <label for="typeWarning" class="lbl-warning type-warning">
                                    <i class="fas fa-exclamation-triangle fa-lg mb-2 d-block"></i> Peringatan
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="type" id="typeDanger" value="danger">
                                <label for="typeDanger" class="lbl-danger type-danger">
                                    <i class="fas fa-times-circle fa-lg mb-2 d-block"></i> Penting
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FITUR: ISI PESAN -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Isi Pesan</label>
                        <textarea name="message" class="form-control rounded-3" rows="6" placeholder="Tulis pesan lengkap di sini..." required style="resize: none;"></textarea>
                    </div>

                    <div class="d-none d-lg-block">
                        <button type="button" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm" onclick="confirmSend()">
                            <i class="fas fa-paper-plane me-2"></i> Kirim Notifikasi
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- FITUR: TARGET PENERIMA -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-users me-2 text-finvera"></i>Target Penerima</h5>
                        <p class="text-muted small mb-0">Pilih siapa yang akan menerima pesan ini.</p>
                    </div>

                    <!-- SUB-FITUR: BROADCAST TOGGLE -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="broadcastMode" name="is_broadcast" value="1">
                        <label class="form-check-label fw-bold small text-dark" for="broadcastMode">Kirim ke Semua (Broadcast)</label>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">

                    <div class="alert alert-info border-0 bg-info bg-opacity-10 small mb-3" id="broadcastAlert" style="display: none;">
                        <i class="fas fa-bullhorn me-2"></i> <strong>Mode Broadcast Aktif:</strong> Pesan akan dikirim ke seluruh pengguna yang terdaftar. Pemilihan manual dinonaktifkan.
                    </div>

                    <!-- SUB-FITUR: TABEL PENERIMA -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="usersTable">
                            <thead class="bg-light small text-muted text-uppercase">
                                <tr>
                                    <th class="table-check ps-3">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                    </th>
                                    <th>Nama Pengguna</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="table-check ps-3">
                                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="form-check-input user-check">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2 text-center small fw-bold text-muted" style="width: 32px; height: 32px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="small text-muted">{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">User</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Submit Button Mobile -->
            <div class="d-block d-lg-none mt-3">
                <button type="button" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm" onclick="confirmSend()">
                    <i class="fas fa-paper-plane me-2"></i> Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // --- LOGIC: DATATABLE ---
        const table = $('#usersTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            paging: true,
            pageLength: 5,
            lengthChange: false,
            info: true,
            columnDefs: [{ orderable: false, targets: 0 }]
        });

        // --- LOGIC: BROADCAST TOGGLE ---
        $('#broadcastMode').change(function() {
            const isBroadcast = this.checked;
            const checkboxes = $('.user-check, #checkAll');

            if (isBroadcast) {
                $('#broadcastAlert').slideDown();
                checkboxes.prop('disabled', true);
                $('#usersTable').addClass('opacity-50 pointer-events-none');
            } else {
                $('#broadcastAlert').slideUp();
                checkboxes.prop('disabled', false);
                $('#usersTable').removeClass('opacity-50 pointer-events-none');
            }
        });

        // --- LOGIC: CHECK ALL ---
        $('#checkAll').change(function() {
            const isChecked = this.checked;
            $('.user-check').prop('checked', isChecked);
        });

        $(document).on('change', '.user-check', function() {
            if (!this.checked) {
                $('#checkAll').prop('checked', false);
            }
        });
    });

    // --- LOGIC: CONFIRMATION & SEND ---
    function confirmSend() {
        const isBroadcast = document.getElementById('broadcastMode').checked;
        const selectedCount = document.querySelectorAll('.user-check:checked').length;

        if (!isBroadcast && selectedCount === 0) {
            Swal.fire('Pilih Penerima', 'Silakan pilih minimal satu pengguna atau aktifkan mode Broadcast.', 'warning');
            return;
        }

        const targetText = isBroadcast ? 'SEMUA PENGGUNA (Broadcast)' : selectedCount + ' Pengguna Terpilih';

        Swal.fire({
            title: 'Kirim Notifikasi?',
            html: `Anda akan mengirim pesan kepada <strong>${targetText}</strong>.<br>Tindakan ini tidak dapat dibatalkan.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3A6D48',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kirim Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim...',
                    didOpen: () => Swal.showLoading()
                });
                document.getElementById('notifForm').submit();
            }
        });
    }
</script>
@endpush
@endsection
