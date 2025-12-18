@extends('layouts.dashboard')

@section('page_title', 'Kotak Masuk')

@section('content')
<style>
    /* Styling List Notifikasi Modern */
    .notif-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid #f0f0f0;
        cursor: pointer;
        background-color: white;
        position: relative;
        overflow: hidden;
    }
    .notif-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-color: #3A6D48;
        z-index: 10;
    }

    /* Styling Status Baca */
    .notif-card.unread {
        background-color: #f0fdf4;
        border-left: 4px solid #3A6D48;
    }
    .notif-card.read {
        background-color: #fff;
        opacity: 0.95;
        border-left: 4px solid transparent;
    }

    .notif-icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        flex-shrink: 0;
    }

    .scrollable-list {
        max-height: 680px;
        overflow-y: auto;
        padding: 10px 15px;
        margin: -10px -15px;
    }
    .scrollable-list::-webkit-scrollbar { width: 5px; }
    .scrollable-list::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }

    /* --- FIX PAGINATION STYLE --- */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    .page-item .page-link {
        color: #3A6D48;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        margin: 0 2px;
    }
    .page-item.active .page-link {
        background-color: #3A6D48;
        border-color: #3A6D48;
        color: white;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    .page-link:hover {
        background-color: #f8f9fa;
        color: #2c5236;
        text-decoration: none;
    }
</style>

<div class="row justify-content-center">
    <!-- Mengembalikan ukuran kolom ke col-12 agar lebar penuh dekat sidebar -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Riwayat Notifikasi</h5>
                    <p class="text-muted small mb-0">Semua pemberitahuan dan aktivitas akun Anda.</p>
                </div>

                @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                        {{ Auth::user()->unreadNotifications->count() }} Belum Dibaca
                    </span>
                @endif
            </div>

            <div class="card-body p-4 bg-light">
                <div class="scrollable-list">
                    <div class="d-flex flex-column gap-3">
                        @forelse($notifications as $notification)
                            <!-- FIX ROUTE: Menggunakan notifications.show -->
                            <a href="{{ route('notifications.show', $notification->id) }}" class="text-decoration-none text-dark">
                                <div class="card notif-card rounded-4 p-3 {{ $notification->read_at ? 'read' : 'unread' }}">
                                    <div class="d-flex align-items-start gap-3">

                                        <!-- Icon -->
                                        <div class="notif-icon-box text-white bg-{{ $notification->data['type'] ?? 'primary' }}">
                                            <i class="fas fa-{{ $notification->data['icon'] ?? 'info' }} fs-5"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 fw-bold {{ $notification->read_at ? 'text-secondary' : 'text-dark' }}">
                                                    {{ $notification->data['title'] }}
                                                </h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <!-- Waktu Bahasa Indonesia -->
                                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-0 text-secondary small lh-base text-truncate">
                                                {{ Str::limit($notification->data['message'], 100) }}
                                            </p>
                                        </div>

                                        <!-- Status Indicator -->
                                        <div class="align-self-center">
                                            @if(!$notification->read_at)
                                                <div class="bg-success rounded-circle" style="width: 10px; height: 10px;" title="Belum Dibaca"></div>
                                            @else
                                                <i class="fas fa-chevron-right text-muted small opacity-50"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <div class="bg-white rounded-circle d-inline-flex p-4 mb-3 text-muted shadow-sm">
                                    <i class="far fa-bell-slash fa-3x opacity-50"></i>
                                </div>
                                <h6 class="text-muted fw-bold">Tidak ada notifikasi.</h6>
                                <p class="small text-muted mb-0">Semua pemberitahuan penting akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($notifications->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        <!-- Memaksa styling bootstrap 5 pagination -->
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
