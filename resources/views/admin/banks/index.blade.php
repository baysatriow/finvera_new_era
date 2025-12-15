@extends('layouts.dashboard')

@section('page_title', 'Rekening Perusahaan')

@section('content')
@push('styles')
<style>
    /* Styling Card Bank */
    .bank-card {
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        position: relative;
        overflow: hidden;
    }
    .bank-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border-color: #3A6D48;
    }
    .bank-card .card-body {
        padding: 1.5rem;
    }

    /* Bank Icon Placeholder */
    .bank-icon {
        width: 50px;
        height: 50px;
        background-color: #f8f9fa;
        color: #3A6D48;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    /* Status Badge */
    .badge-status {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-active { background-color: #e8f5e9; color: #2e7d32; }
    .status-inactive { background-color: #f8f9fa; color: #6c757d; }

    /* Action Buttons */
    .btn-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid #eee;
        color: #6c757d;
        transition: all 0.2s;
        background: white;
        padding: 0;
    }
    .btn-icon:hover { background-color: #f8f9fa; color: #333; }
    .btn-icon.primary-btn { color: #2e7d32; border-color: #2e7d32; background-color: #f0fdf4; }
    .btn-icon.primary-btn:hover { background-color: #2e7d32; color: white; }
    .btn-icon.edit:hover { color: #0d6efd; border-color: #0d6efd; }
    .btn-icon.delete:hover { color: #dc3545; border-color: #dc3545; }

    /* Form Styles */
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3A6D48;
        box-shadow: 0 0 0 0.2rem rgba(58, 109, 72, 0.1);
    }
</style>
@endpush

<div class="row">
    <div class="col-12 mb-4">
        <!-- Header Section -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Daftar Rekening Pembayaran</h4>
                    <p class="text-muted small mb-0">Kelola rekening tujuan transfer untuk pembayaran cicilan pengguna.</p>
                </div>
                <button class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addBankModal">
                    <i class="fas fa-plus me-2"></i> Tambah Rekening
                </button>
            </div>
        </div>
    </div>

    <!-- List Rekening Cards -->
    @if($banks->count() > 0)
        @foreach($banks as $bank)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card bank-card h-100 {{ $bank->is_active ? 'border-success' : '' }}">
                <!-- Status Badge -->
                <span class="badge-status {{ $bank->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $bank->is_active ? 'Penerima Utama' : 'Arsip' }}
                </span>

                <div class="card-body">
                    <div class="bank-icon shadow-sm {{ $bank->is_active ? 'bg-success text-white' : '' }}">
                        <i class="fas fa-university"></i>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $bank->bank_name }}</h5>
                    <div class="font-monospace text-secondary fs-5 mb-2">{{ $bank->account_number }}</div>
                    <div class="small text-muted text-uppercase fw-bold mb-4">
                        <i class="fas fa-user-circle me-1"></i> {{ $bank->account_holder }}
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <!-- Action Area -->
                        <div class="d-flex align-items-center gap-2">
                             @if(!$bank->is_active)
                                <form action="{{ route('admin.banks.primary', $bank->id) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-icon primary-btn btn-set-primary" title="Jadikan Utama">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                             @else
                                <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                             @endif
                        </div>

                        <div class="d-flex gap-2">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-icon edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBankModal"
                                    data-id="{{ $bank->id }}"
                                    data-name="{{ $bank->bank_name }}"
                                    data-number="{{ $bank->account_number }}"
                                    data-holder="{{ $bank->account_holder }}"
                                    data-active="{{ $bank->is_active }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Delete Button -->
                            <button type="button" class="btn btn-icon delete btn-delete" data-id="{{ $bank->id }}" data-name="{{ $bank->bank_name }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <form id="delete-form-{{ $bank->id }}" action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="fas fa-university fa-3x text-muted opacity-50 mb-3"></i>
                <h6 class="text-muted fw-bold">Belum ada rekening perusahaan.</h6>
                <p class="small text-muted">Tambahkan rekening agar pengguna dapat melakukan pembayaran.</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Tambah Rekening -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Rekening Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.banks.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Bank / E-Wallet</label>
                        <select name="bank_name" class="form-select" required>
                            <option value="">Pilih...</option>
                            @foreach(['BCA','Mandiri','BRI','BNI','BSI','Jago','SeaBank','GoPay','OVO','Dana','ShopeePay'] as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nomor Rekening</label>
                        <input type="number" name="account_number" class="form-control" placeholder="Contoh: 1234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Atas Nama</label>
                        <input type="text" name="account_holder" class="form-control" placeholder="PT FinVera Indonesia" required>
                    </div>

                    <div class="alert alert-light border small text-muted">
                        <i class="fas fa-info-circle me-1"></i> Jika Anda mencentang "Jadikan Utama", rekening lain akan otomatis dinonaktifkan.
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="addActive">
                        <label class="form-check-label small fw-bold" for="addActive">Jadikan Rekening Utama</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold py-2">Simpan Rekening</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Rekening -->
<div class="modal fade" id="editBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>Edit Rekening</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Bank</label>
                        <select name="bank_name" id="edit_bank_name" class="form-select" required>
                            @foreach(['BCA','Mandiri','BRI','BNI','BSI','Jago','SeaBank','GoPay','OVO','Dana','ShopeePay'] as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nomor Rekening</label>
                        <input type="number" name="account_number" id="edit_account_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Atas Nama</label>
                        <input type="text" name="account_holder" id="edit_account_holder" class="form-control" required>
                    </div>
                    <div class="form-check form-switch mb-4 p-3 bg-light rounded-3 border">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" value="1" id="edit_is_active" style="float: none;">
                        <label class="form-check-label fw-bold small text-dark" for="edit_is_active">
                            Set Sebagai Rekening Utama
                        </label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2">Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Handle Edit Modal Population
        var editModal = document.getElementById('editBankModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var number = button.getAttribute('data-number');
            var holder = button.getAttribute('data-holder');
            var active = button.getAttribute('data-active');

            var form = document.getElementById('editForm');
            form.action = '/admin/banks/' + id;

            document.getElementById('edit_bank_name').value = name;
            document.getElementById('edit_account_number').value = number;
            document.getElementById('edit_account_holder').value = holder;
            document.getElementById('edit_is_active').checked = (active == 1);
        });

        // SWAL Set Primary Confirmation
        const setPrimaryButtons = document.querySelectorAll('.btn-set-primary');
        setPrimaryButtons.forEach(button => {
            button.addEventListener('click', function() {
                var form = this.closest('form');
                Swal.fire({
                    title: 'Jadikan Utama?',
                    text: "Rekening ini akan menjadi tujuan transfer utama. Rekening lain akan dinonaktifkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3A6D48',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // SWAL Delete Confirmation
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Hapus Rekening?',
                    html: "Anda akan menghapus rekening <strong>" + name + "</strong>.<br>User tidak akan bisa transfer ke sini lagi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
