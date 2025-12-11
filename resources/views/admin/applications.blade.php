@extends('layouts.dashboard')

@section('page_title', 'Persetujuan Pinjaman')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Peminjam</th>
                        <th>Pengajuan</th>
                        <th>Data Keuangan</th>
                        <th>Aset Jaminan</th>
                        <th>AI Score</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $app->user->name }}</div>
                            <div class="small text-muted mb-1">{{ $app->user->job }}</div>
                            <span class="badge bg-light text-dark border">User ID: {{ $app->user->id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-success">Rp {{ number_format($app->amount, 0, ',', '.') }}</div>
                            <div class="small text-muted">{{ $app->tenor }} Bulan</div>
                            <small class="text-muted d-block mt-1 fst-italic">"{{ Str::limit($app->purpose, 20) }}"</small>
                        </td>
                        <td>
                            <div class="small">Gaji: <span class="fw-bold">Rp {{ number_format($app->user->monthly_income, 0, ',', '.') }}</span></div>
                            <div class="small">Kerja: {{ $app->user->employment_duration }} Bln</div>
                        </td>
                        <td>
                            <div class="small fw-bold">Rp {{ number_format($app->asset_value, 0, ',', '.') }}</div>
                            <a href="#" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Lihat Foto</a>
                        </td>
                        <td>
                            @php
                                $scoreColor = $app->ai_score >= 70 ? 'success' : ($app->ai_score >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="fw-bold text-{{ $scoreColor }}">{{ $app->ai_score }} / 100</div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-2">
                                <!-- Tombol Reject (Trigger Modal) -->
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- Form Approve -->
                                <form action="{{ route('admin.approve', $app->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui pinjaman ini? Dana akan segera dicairkan.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success px-3">
                                        <i class="fas fa-check me-1"></i> Setuju
                                    </button>
                                </form>
                            </div>

                            <!-- Modal Reject -->
                            <div class="modal fade text-start" id="rejectModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.reject', $app->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fs-6 fw-bold">Tolak Pengajuan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted">Alasan Penolakan</label>
                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Dokumen aset buram, Gaji tidak mencukupi..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Tolak Pinjaman</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-3 text-success opacity-50"></i>
                            <p class="mb-0">Tidak ada pengajuan yang menunggu persetujuan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
