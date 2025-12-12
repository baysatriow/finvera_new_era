<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinVera - Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Variables & Base --- */
        :root {
            --finvera-primary: #3A6D48;
            --finvera-dark: #2c5236;
            --finvera-bg: #F5F7FA;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--finvera-bg);
            overflow-x: hidden;
            transition: all 0.3s;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: white;
            border-right: 1px solid #eee;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 25px;
            display: flex;
            align-items: center;
            color: var(--finvera-primary);
            font-weight: 700;
            font-size: 1.25rem;
            text-decoration: none;
            border-bottom: 1px solid #f8f9fa;
        }
        .sidebar-menu {
            padding: 20px 15px;
        }
        .menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #adb5bd;
            font-weight: 600;
            margin-bottom: 10px;
            padding-left: 15px;
            letter-spacing: 0.5px;
            margin-top: 20px;
        }
        .menu-label:first-child { margin-top: 0; }

        /* Navigation Links */
        .nav-link {
            color: #6c757d;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        .nav-link i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--finvera-primary);
        }
        .nav-link.active {
            background-color: var(--finvera-primary);
            color: white;
            box-shadow: 0 4px 6px rgba(58, 109, 72, 0.2);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px 30px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .top-navbar {
            background: white;
            border-radius: 12px;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- User Profile & Notification --- */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Notification Dropdown */
        .notification-scroll::-webkit-scrollbar { width: 4px; }
        .notification-scroll::-webkit-scrollbar-thumb { background-color: #e0e0e0; border-radius: 4px; }

        .notif-item {
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
        }
        .notif-item:hover { background-color: #f8f9fa; }
        .notif-item.unread { background-color: #f0fdf4; }
        .notif-item.unread:hover { background-color: #e8f5e9; }

        .btn-view-all {
            color: var(--finvera-primary);
            transition: all 0.2s;
        }
        .btn-view-all:hover {
            background-color: #f8f9fa;
            color: var(--finvera-dark);
        }

        /* --- Responsive Logic --- */
        body.sidebar-closed .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
        body.sidebar-closed .main-content { margin-left: 0; }

        @media (max-width: 768px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-leaf me-2"></i> FinVera
        </a>

        <div class="sidebar-menu">
            @if(Auth::user()->role === 'admin')
                <div class="menu-label">Administrator</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i> Persetujuan Pinjaman
                </a>

                <div class="menu-label">Manajemen</div>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Manajemen User
                </a>
                <a href="{{ route('admin.borrowers.index') }}" class="nav-link {{ request()->routeIs('admin.borrowers.*') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i> Data Peminjam
                </a>
                <a href="{{ route('admin.disbursement.index') }}" class="nav-link {{ request()->routeIs('admin.disbursement.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> Data Pembayaran
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Kelola Notifikasi
                </a>

            @else
                <div class="menu-label">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="{{ route('loans.create') }}" class="nav-link {{ request()->routeIs('loans.create') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd"></i> Ajukan Pinjaman
                </a>
                <a href="{{ route('bank.index') }}" class="nav-link {{ request()->routeIs('bank.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i> Rekening Bank
                </a>

                <div class="menu-label">Transaksi</div>
                <a href="{{ route('history') }}" class="nav-link {{ request()->routeIs('history') || request()->routeIs('loans.show') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Pinjaman
                </a>
                <a href="{{ route('installments.index') }}" class="nav-link {{ request()->routeIs('installments.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i> Cicilan Saya
                </a>

                <div class="menu-label">Verifikasi</div>
                <a href="{{ route('kyc.create') }}" class="nav-link {{ request()->routeIs('kyc.*') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i>
                    <span class="flex-grow-1">Identitas (KYC)</span>
                    @php $status = Auth::user()->kyc_status; @endphp
                    @if($status == 'verified')
                        <i class="fas fa-check-circle text-success ms-2" title="Terverifikasi"></i>
                    @elseif($status == 'pending')
                        <i class="fas fa-clock text-warning ms-2" title="Sedang Diproses"></i>
                    @else
                        <i class="fas fa-exclamation-circle text-danger ms-2" title="Belum Verifikasi"></i>
                    @endif
                </a>
            @endif

            <div class="menu-label">Akun</div>
            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="far fa-user"></i> Profil Saya
            </a>

            <form action="{{ route('logout') }}" method="POST" class="mt-4 pt-4 border-top">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border-0 text-dark shadow-sm" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">

                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-bell fa-lg"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0" style="width: 360px;">
                        <li class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Notifikasi</h6>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                    {{ Auth::user()->unreadNotifications->count() }} Baru
                                </span>
                            @endif
                        </li>

                        <div class="notification-scroll" style="max-height: 350px; overflow-y: auto;">
                            @forelse(Auth::user()->notifications->take(5) as $notification)
                                <li>
                                    <a class="dropdown-item px-4 py-3 border-bottom d-flex align-items-start gap-3 notif-item {{ $notification->read_at ? '' : 'unread' }}" href="{{ route('notifications.show', $notification->id) }}">
                                        @php
                                            $type = $notification->data['type'] ?? 'info';
                                            $bgColor = match($type) {
                                                'danger' => '#dc3545',
                                                'warning' => '#ffc107',
                                                default => '#3A6D48'
                                            };
                                            $icon = $notification->data['icon'] ?? 'info';
                                        @endphp
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white"
                                             style="width: 38px; height: 38px; background-color: {{ $bgColor }};">
                                            <i class="fas fa-{{ $icon }} small"></i>
                                        </div>

                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 text-dark small fw-bold text-truncate" style="max-width: 160px;">
                                                    {{ $notification->data['title'] }}
                                                </h6>
                                                <small class="text-muted" style="font-size: 0.65rem;">
                                                    {{ $notification->created_at->locale('id')->diffForHumans(null, true) }}
                                                </small>
                                            </div>
                                            <p class="mb-0 text-secondary small lh-sm text-truncate">
                                                {{ $notification->data['message'] }}
                                            </p>
                                        </div>

                                        @if(!$notification->read_at)
                                            <div class="align-self-center">
                                                <span class="d-inline-block rounded-circle bg-danger" style="width: 8px; height: 8px;"></span>
                                            </div>
                                        @endif
                                    </a>
                                </li>
                            @empty
                                <li class="p-5 text-center text-muted">
                                    <i class="far fa-bell-slash fa-2x mb-3 opacity-25"></i>
                                    <p class="small mb-0">Tidak ada notifikasi baru.</p>
                                </li>
                            @endforelse
                        </div>

                        <li>
                            <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small fw-bold py-3 btn-view-all rounded-bottom-4">
                                Lihat Semua Notifikasi <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="border-start mx-2 h-50"></div>

                <a href="{{ route('profile') }}" class="text-decoration-none">
                    <div class="user-profile">
                        <div class="text-end d-none d-sm-block lh-1">
                            <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</small>
                        </div>
                        <div class="user-avatar {{ Auth::user()->role == 'admin' ? 'bg-danger text-danger bg-opacity-10' : 'bg-success text-success bg-opacity-25' }}">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle Logic
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const body = document.body;

            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.innerWidth >= 768) {
                    body.classList.toggle('sidebar-closed');
                } else {
                    sidebar.classList.toggle('show');
                }
            });

            // SweetAlert2 Alerts
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#3A6D48' });
            @endif

            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#d33' });
            @endif

            @if(session('warning'))
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ session('warning') }}", confirmButtonColor: '#ffc107' });
            @endif

            // Laravel Validation Error Display
            @if($errors->any())
                let errorHtml = '<ul class="text-start ps-4 mb-0">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Mohon Periksa Kembali',
                    html: errorHtml,
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
