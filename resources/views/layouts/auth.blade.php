<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FinVera') }} - @yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --finvera-primary: #3A6D48;
            --finvera-secondary: #E8F5E9;
            --finvera-accent: #FFC107;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .text-finvera { color: var(--finvera-primary); }
        .bg-finvera { background-color: var(--finvera-primary); }

        .navbar {
            padding: 15px 0;
            background-color: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .btn-finvera {
            background-color: var(--finvera-primary);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-finvera:hover {
            background-color: #2e5739;
            color: white;
            transform: translateY(-1px);
        }
        .btn-outline-finvera {
            border: 2px solid var(--finvera-primary);
            color: var(--finvera-primary);
            padding: 6px 18px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-outline-finvera:hover {
            background-color: var(--finvera-primary);
            color: white;
        }

        .card-auth {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 40px;
        }
        .form-control, .form-select {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--finvera-primary);
            box-shadow: 0 0 0 0.2rem rgba(58, 109, 72, 0.25);
        }

        /* Select2 Custom Fix */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 8px;
            padding: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-finvera" href="{{ url('/') }}">
                <i class="fas fa-leaf me-2"></i>FinVera
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#tentang') }}">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#produk') }}">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#kontak') }}">Kontak</a></li>
                </ul>
                <div class="d-flex gap-2">
                    @if(!request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="btn btn-outline-finvera">Masuk</a>
                    @endif
                    @if(!request()->routeIs('register'))
                        <a href="{{ route('register') }}" class="btn btn-finvera">Daftar</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-auth">
                    <div class="row g-0">
                        <!-- Sisi Kiri: Gambar Ilustrasi -->
                        <div class="col-md-5 d-none d-md-block position-relative" style="background-image: url('https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80'); background-size: cover; background-position: center;">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(58, 109, 72, 0.85);"></div>
                            <div class="position-absolute top-50 start-50 translate-middle text-center text-white p-4 w-100">
                                <i class="fas fa-leaf fa-3x mb-3 text-warning"></i>
                                <h2 class="fw-bold">FinVera</h2>
                                <p class="lead small">Solusi Keuangan Syariah Cepat, Aman, & Berkah.</p>
                            </div>
                        </div>
                        <!-- Sisi Kanan: Konten Form -->
                        <div class="col-md-7">
                            <div class="card-body p-4 p-lg-5">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3 text-muted small pb-4">
                    &copy; {{ date('Y') }} FinVera Syariah Technology. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#3A6D48' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#d33' });
        @endif
        @if($errors->any())
            let errorMsg = '<ul style="text-align: left;">';
            @foreach ($errors->all() as $error) errorMsg += '<li>{{ $error }}</li>'; @endforeach
            errorMsg += '</ul>';
            Swal.fire({ icon: 'error', title: 'Periksa Kembali', html: errorMsg, confirmButtonColor: '#3A6D48' });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
