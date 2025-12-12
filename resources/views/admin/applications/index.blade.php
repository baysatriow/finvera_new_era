@extends('layouts.dashboard')

@section('page_title', 'Persetujuan Pinjaman')

@section('content')

    @push('styles')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .table-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background-color: white;
        }

        .table thead th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            border-top: none;
        }

        .table tbody td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            margin-left: 10px;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3A6D48;
            box-shadow: 0 0 0 2px rgba(58, 109, 72, 0.1);
            outline: none;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 6px 32px 6px 12px;
            font-size: 0.875rem;
        }

        .page-item.active .page-link {
            background-color: #3A6D48;
            border-color: #3A6D48;
            color: white;
        }
        .page-link {
            color: #374151;
            border-radius: 6px;
            margin: 0 2px;
            border: 1px solid #e5e7eb;
        }
        .page-link:hover {
            background-color: #f3f4f6;
            color: #3A6D48;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">

            <div class="card border-0 table-card">

                <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Daftar Pengajuan Pending</h5>
                        <p class="text-muted small mb-0">Tinjau dan proses pengajuan pinjaman yang masuk.</p>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill shadow-sm border border-warning border-opacity-25">
                        <i class="fas fa-clock me-1"></i> Pending: {{ $applications->count() }}
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100 mb-0" id="applicationsTable">
                            <thead>
                                <tr>
                                    <th class="ps-4">Peminjam</th>
                                    <th>Nominal & Tanggal</th>
                                    <th>Tenor</th>
                                    <th>Gaji Bulanan</th>
                                    <th>AI Score</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-finvera border border-gray-100" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                                {{ substr($app->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $app->user->name }}</div>
                                                <div class="small text-muted">{{ $app->user->job }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-success">Rp {{ number_format($app->amount, 0, ',', '.') }}</div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $app->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-gray-100 text-dark border border-gray-200 rounded-pill fw-normal px-3">
                                            {{ $app->tenor }} Bulan
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-dark fw-medium">Rp {{ number_format($app->user->monthly_income, 0, ',', '.') }}</span>
                                    </td>

                                    <td>
                                        @php
                                            $scoreColor = $app->ai_score >= 75 ? 'success' : ($app->ai_score >= 50 ? 'warning' : 'danger');
                                            $icon = $app->ai_score >= 75 ? 'check-circle' : ($app->ai_score >= 50 ? 'exclamation-circle' : 'times-circle');
                                        @endphp
                                        <span class="badge bg-{{ $scoreColor }} bg-opacity-10 text-{{ $scoreColor }} rounded-pill px-3 border border-{{ $scoreColor }} border-opacity-25 d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-{{ $icon }}"></i> {{ $app->ai_score }}
                                        </span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-sm btn-outline-finvera rounded-pill px-4 fw-bold shadow-sm transition-transform hover:scale-105">
                                            Detail <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#applicationsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                    searchPlaceholder: "Cari nama, pekerjaan...",
                    lengthMenu: "Tampilkan _MENU_ data"
                },
                order: [[ 4, "desc" ]],
                lengthMenu: [10, 25, 50],
                pageLength: 10,
                dom: '<"p-4 d-flex justify-content-between align-items-center flex-wrap gap-3"lf>rt<"p-4 d-flex justify-content-between align-items-center flex-wrap gap-3"ip>',

                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });
        });
    </script>
    @endpush

@endsection
