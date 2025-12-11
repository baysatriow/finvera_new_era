@extends('layouts.dashboard')

@section('page_title', 'Detail Pinjaman')

@section('content')
<div class="row g-4">
    <!-- KOLOM KIRI: INFO UTAMA & ASET -->
    <div class="col-lg-8">

        <!-- Card Status & Info -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase fw-bold ls-1">ID Pengajuan</small>
                        <h5 class="fw-bold text-dark mb-0">#{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</h5>
                    </div>

                    <div class="d-flex gap-2">
                        @php
                            $statusClass = match($loan->status) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                'paid' => 'primary',
                                default => 'secondary'
                            };
                            $statusLabel = match($loan->status) {
                                'approved' => 'Aktif (Disetujui)',
                                'pending' => 'Menunggu Review',
                                'rejected' => 'Ditolak',
                                'paid' => 'Lunas Selesai',
                                default => $loan->status
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} px-4 py-2 rounded-pill fs-6">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-1 text-uppercase">Nominal Pinjaman</label>
                        <h3 class="fw-bold text-finvera mb-0">Rp {{ number_format($loan->amount, 0, ',', '.') }}</h3>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-1 text-uppercase">Tenor & Waktu</label>
                        <div class="fw-bold text-dark fs-5">{{ $loan->tenor }} Bulan</div>
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ $loan->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted fw-bold mb-2 text-uppercase">Tujuan Penggunaan</label>
                        <div class="bg-light p-3 rounded-3 border-start border-4 border-success text-dark">
                            {{ $loan->purpose }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Ajukan Lagi (Hanya jika Lunas/Ditolak) -->
            @if(in_array($loan->status, ['paid', 'rejected', 'canceled']))
            <div class="card-footer bg-white p-4 border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Pinjaman ini telah selesai/ditutup.</span>
                    <a href="{{ route('loans.create') }}" class="btn btn-finvera shadow-sm px-4 fw-bold">
                        <i class="fas fa-redo me-2"></i> Ajukan Pinjaman Baru
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Card Dokumen Aset (VISUAL) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-images me-2 text-warning"></i>Bukti Aset Jaminan</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label class="small text-muted fw-bold mb-2 d-block">Foto Dokumen</label>
                            <div class="overflow-hidden rounded-3 border bg-white d-flex align-items-center justify-content-center position-relative group-hover" style="height: 200px;">
                                @if($loan->asset_document_path)
                                    <img src="{{ asset('storage/' . $loan->asset_document_path) }}" alt="Dokumen Aset" class="w-100 h-100 object-fit-cover hover-zoom">
                                    <a href="{{ asset('storage/' . $loan->asset_document_path) }}" target="_blank" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-2 opacity-75">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak ada gambar</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label class="small text-muted fw-bold mb-2 d-block">Foto Selfie Aset</label>
                            <div class="overflow-hidden rounded-3 border bg-white d-flex align-items-center justify-content-center position-relative group-hover" style="height: 200px;">
                                @if($loan->asset_selfie_path)
                                    <img src="{{ asset('storage/' . $loan->asset_selfie_path) }}" alt="Selfie Aset" class="w-100 h-100 object-fit-cover hover-zoom">
                                    <a href="{{ asset('storage/' . $loan->asset_selfie_path) }}" target="_blank" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-2 opacity-75">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak ada gambar</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center px-2 pt-2">
                            <span class="small text-muted">Estimasi Nilai Aset:</span>
                            <span class="fw-bold text-dark fs-5">Rp {{ number_format($loan->asset_value, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Jadwal Cicilan (Hanya jika Approved/Active/Paid) -->
        @if(in_array($loan->status, ['approved', 'active', 'paid']) && $loan->loan)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-calendar-check me-2 text-finvera"></i>Jadwal Pembayaran</h6>
                <span class="badge bg-light text-dark border font-monospace">KODE: {{ $loan->loan->loan_code }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-muted text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Bulan Ke</th>
                            <th class="py-3">Jatuh Tempo</th>
                            <th class="py-3">Jumlah</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->loan->installments as $installment)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $installment->installment_number }}</td>
                            <td>{{ $installment->due_date->format('d M Y') }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($installment->amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($installment->status == 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                                @elseif($installment->status == 'late')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Terlambat</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Belum</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($installment->status != 'paid' && $loan->status != 'paid')
                                    <form action="{{ route('installments.pay', $installment->id) }}" method="POST" onsubmit="return confirm('Bayar cicilan bulan ke-{{ $installment->installment_number }}?');">
                                        @csrf
                                        <button class="btn btn-sm btn-finvera text-white px-3 rounded-pill">Bayar</button>
                                    </form>
                                @elseif($installment->status == 'paid')
                                    <small class="text-success fw-bold"><i class="fas fa-check-double me-1"></i> Lunas</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    <!-- KOLOM KANAN: ANALISIS AI -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-success bg-opacity-10 py-3 px-4 border-bottom-0">
                <h6 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Analisis Kredit AI</h6>
            </div>
            <div class="card-body p-4 text-center">

                <!-- Credit Score Circle (Perfectly Centered) -->
                <div class="mb-4 d-flex justify-content-center">
                    <div class="position-relative" style="width: 140px; height: 140px;">
                        <svg width="140" height="140" viewBox="0 0 120 120" class="transform -rotate-90 w-100 h-100">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#f1f3f5" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $loan->ai_score >= 70 ? '#3A6D48' : '#dc3545' }}" stroke-width="8"
                                    stroke-dasharray="339.292"
                                    stroke-dashoffset="{{ 339.292 - (339.292 * $loan->ai_score / 100) }}"
                                    stroke-linecap="round" style="transition: stroke-dashoffset 1s ease-in-out;"/>
                        </svg>

                        <!-- Text Center Overlay -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <h1 class="fw-bold mb-0 text-dark display-6" style="line-height: 1;">{{ $loan->ai_score }}</h1>
                            <span class="text-muted fw-bold" style="font-size: 0.8rem;">SKOR AI</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="badge {{ $loan->ai_score >= 70 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill fs-6">
                        {{ $loan->ai_score >= 70 ? 'Sangat Baik' : 'Berisiko Tinggi' }}
                    </span>
                </div>

                <!-- Penjelasan AI (User Message) -->
                <div class="alert alert-light border border-secondary border-opacity-10 rounded-3 mb-0 text-start">
                    <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Alasan Penilaian</label>
                    <p class="mb-0 small text-dark lh-base">
                        @if($loan->ai_user_message)
                            <i class="fas fa-quote-left text-muted me-2 opacity-50"></i>
                            {{ $loan->ai_user_message }}
                        @else
                            <em class="text-muted">Analisis detail tidak tersedia untuk pengajuan ini.</em>
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <!-- Bantuan -->
        <div class="card border-0 shadow-sm rounded-4 bg-light text-center">
            <div class="card-body p-4">
                <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 shadow-sm text-finvera">
                    <i class="fas fa-headset fa-2x"></i>
                </div>
                <h6 class="fw-bold text-dark">Butuh Bantuan?</h6>
                <p class="small text-muted mb-3">Hubungi layanan pelanggan jika ada kendala.</p>
                <a href="mailto:support@finvera.com" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                    support@finvera.com
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .object-fit-cover { object-fit: cover; }
    .hover-zoom { transition: transform 0.3s ease; }
    .group-hover:hover .hover-zoom { transform: scale(1.05); }
    /* Memastikan tabel DataTables (jika ada) atau tabel biasa tetap rapi */
    td { vertical-align: middle; }
</style>
@endsection
