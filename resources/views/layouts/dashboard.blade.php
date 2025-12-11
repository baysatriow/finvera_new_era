<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinVera - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
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

        /* Sidebar Styling */
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

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px 30px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Top Navbar */
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
            background-color: #ffeeba;
            color: #d63384;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Logic Toggle Sidebar */
        body.sidebar-closed .sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        body.sidebar-closed .main-content {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-leaf me-2"></i> FinVera
        </a>

        <div class="sidebar-menu">

            @if(Auth::user()->role === 'admin')
                <!-- MENU ADMIN -->
                <div class="menu-label mt-0">Administrator</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>

                                <!-- MENU BARU: DATA PEMINJAM -->
                <a href="{{ route('admin.borrowers.index') }}" class="nav-link {{ request()->routeIs('admin.borrowers.*') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i> Data Peminjam
                </a>
                <!-- END MENU BARU -->

                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i> Persetujuan Pinjaman
                </a>

                <div class="menu-label">Manajemen</div>

                <!-- Placeholder Routes untuk menu yang belum ada controllernya -->
                <!-- UPDATE LINK INI -->
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Manajemen User
                </a>
                <!-- UPDATE LINK INI -->
                <a href="{{ route('admin.disbursement.index') }}" class="nav-link {{ request()->routeIs('admin.disbursement.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> Manage Disbursement
                </a>
                <!-- UPDATE LINK INI -->
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>

            @else
                <!-- MENU BORROWER (User Biasa) -->
                <div class="menu-label mt-0">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>

                <a href="{{ route('bank.index') }}" class="nav-link {{ request()->routeIs('bank.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i> Rekening Bank
                </a>

                <div class="menu-label">Verifikasi</div>
                <a href="{{ route('kyc.create') }}" class="nav-link {{ request()->routeIs('kyc.*') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i>
                    <span class="flex-grow-1">Identitas (KYC)</span>
                    @if(Auth::user()->kyc_status == 'verified')
                        <i class="fas fa-check-circle text-success ms-2" title="Terverifikasi"></i>
                    @elseif(Auth::user()->kyc_status == 'pending')
                        <i class="fas fa-clock text-warning ms-2" title="Sedang Diproses"></i>
                    @else
                        <i class="fas fa-exclamation-circle text-danger ms-2" title="Belum Verifikasi"></i>
                    @endif
                </a>

                <div class="menu-label">Akun</div>
                <a href="{{ route('history') }}" class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat
                </a>
            @endif

            <!-- Shared Profile Menu -->
            <div class="menu-label">Pengaturan</div>
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border-0 text-dark shadow-sm" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
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
        // Sidebar Toggle
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

        // SweetAlert2 Global
        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#3A6D48' }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#d33' }); @endif
        @if(session('warning')) Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ session('warning') }}", confirmButtonColor: '#ffc107' }); @endif
    </script>
    @stack('scripts')
</body>
</html>
