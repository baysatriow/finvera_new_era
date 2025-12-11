<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ============================================================
     META & CONFIG
     ============================================================ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FinVera') }} - Solusi Keuangan Syariah</title>

    <!-- ============================================================
     ASSETS (CSS & FONTS)
     ============================================================ -->
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ============================================================
     CUSTOM STYLES
     ============================================================ -->
    <style>
        :root {
            --finvera-primary: #3A6D48;
            --finvera-dark: #2c5236;
            --finvera-light: #E8F5E9;
            --finvera-accent: #FFC107;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* --- Utilities --- */
        .text-finvera { color: var(--finvera-primary); }
        .bg-finvera { background-color: var(--finvera-primary); }

        /* --- Buttons --- */
        .btn-finvera {
            background-color: var(--finvera-primary);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-finvera:hover {
            background-color: var(--finvera-dark);
            color: white;
            transform: translateY(-2px);
        }
        .btn-outline-finvera {
            border: 2px solid var(--finvera-primary);
            color: var(--finvera-primary);
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-outline-finvera:hover {
            background-color: var(--finvera-primary);
            color: white;
        }

        /* --- Navbar --- */
        .navbar {
            padding: 15px 0;
            background-color: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 100px 0 80px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-img {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* --- Features Cards --- */
        .feature-card {
            padding: 30px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            height: 100%;
            border-bottom: 4px solid transparent;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            border-bottom: 4px solid var(--finvera-primary);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--finvera-light);
            color: var(--finvera-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* --- Product Section --- */
        .product-section {
            background-color: var(--finvera-primary);
            color: white;
            padding: 80px 0;
        }

        /* --- Footer --- */
        footer {
            background-color: #212529;
            color: #adb5bd;
            padding: 60px 0 20px;
        }
        footer h5 { color: white; font-weight: 700; margin-bottom: 20px; }
        footer a { color: #adb5bd; text-decoration: none; transition: 0.3s; }
        footer a:hover { color: var(--finvera-accent); }
    </style>
</head>
<body>

    <!-- ============================================================
     NAVBAR
     ============================================================ -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-finvera" href="#">
                <i class="fas fa-leaf me-2"></i>FinVera
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="#produk">Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-finvera">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-finvera">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================
     HERO SECTION
     ============================================================ -->
    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="badge bg-success bg-opacity-10 text-success mb-3 px-3 py-2 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> Terdaftar & Diawasi (Simulasi)
                    </span>
                    <h1 class="display-4 fw-bold mb-3">Solusi Keuangan Syariah <br><span class="text-finvera">Cepat & Amanah</span></h1>
                    <p class="lead text-muted mb-4">
                        Dapatkan pembiayaan Qardh tanpa bunga hingga <strong>Rp 20 Juta</strong>. Proses cepat dengan teknologi AI, transparan, dan sesuai prinsip syariah.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('register') }}" class="btn btn-finvera btn-lg shadow-sm">Ajukan Pinjaman <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="#produk" class="btn btn-outline-secondary btn-lg">Pelajari Dulu</a>
                    </div>
                    <div class="mt-4 pt-3 border-top d-flex gap-4 text-muted small">
                        <div><i class="fas fa-shield-alt text-finvera me-1"></i> Data Aman</div>
                        <div><i class="fas fa-bolt text-finvera me-1"></i> Cair Cepat</div>
                        <div><i class="fas fa-star-and-crescent text-finvera me-1"></i> 100% Syariah</div>
                    </div>
                </div>
                <div class="col-lg-6 position-relative">
                    <img src="assets/images/landingpage.png" alt="FinVera App" class="img-fluid hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
     FEATURES SECTION (TENTANG)
     ============================================================ -->
    <section id="tentang" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-finvera fw-bold text-uppercase">Kenapa Memilih Kami?</h6>
                <h2 class="fw-bold">Keunggulan FinVera</h2>
            </div>
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-hand-holding-usd"></i></div>
                        <h4>Tanpa Riba (Bunga)</h4>
                        <p class="text-muted">Kami menggunakan akad Qardh. Anda hanya mengembalikan pokok pinjaman ditambah biaya administrasi yang transparan di awal.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-robot"></i></div>
                        <h4>Teknologi AI</h4>
                        <p class="text-muted">Proses verifikasi (KYC) dan penilaian kredit didukung oleh Artificial Intelligence untuk kecepatan dan akurasi tinggi.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="fas fa-lock"></i></div>
                        <h4>Aman & Terpercaya</h4>
                        <p class="text-muted">Data pribadi Anda dilindungi dengan enkripsi tingkat tinggi dan tidak akan disalahgunakan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
     PRODUCT SECTION
     ============================================================ -->
    <section id="produk" class="product-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-3">Produk Unggulan: <br>Dana Talangan (Qardh)</h2>
                    <p class="opacity-75 mb-4">Solusi dana cepat untuk kebutuhan mendesak Anda. Tanpa jaminan yang memberatkan dan proses yang mudah.</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i> Limit hingga <strong>Rp 20.000.000</strong></li>
                        <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i> Tenor fleksibel 1 - 24 Bulan</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-warning me-2"></i> Pencairan < 24 Jam</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-light text-success fw-bold">Mulai Pengajuan</a>
                </div>
                <div class="col-lg-7">
                    <div class="bg-white text-dark p-4 rounded-3 shadow-lg">
                        <h5 class="fw-bold border-bottom pb-3 mb-3">Simulasi Pembiayaan</h5>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Plafond Pinjaman</small>
                                <span class="fw-bold fs-5">Rp 5.000.000</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Tenor</small>
                                <span class="fw-bold fs-5">3 Bulan</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Biaya Admin</small>
                                <span class="fw-bold fs-5">Rp 2.000</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Total Bayar</small>
                                <span class="fw-bold fs-5 text-finvera">Rp 5.002.000</span>
                            </div>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-info-circle me-1"></i> Tidak ada bunga berbunga. Biaya admin dibayar satu kali di awal atau digabung.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
     FOOTER
     ============================================================ -->
    <footer id="kontak">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white"><i class="fas fa-leaf me-2 text-finvera"></i>FinVera</h5>
                    <p class="small">Platform teknologi finansial syariah yang bertujuan membantu masyarakat mendapatkan akses pembiayaan yang adil dan transparan.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Navigasi</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Beranda</a></li>
                        <li><a href="#produk">Produk</a></li>
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Jl. Syariah No. 10, Jakarta Selatan</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> support@finvera.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +62 21 5555 8888</li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Legalitas</h5>
                    <p class="small">Terdaftar di Kementerian Hukum & HAM (Simulasi).</p>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 mt-4 text-center small">
                &copy; {{ date('Y') }} FinVera Syariah Technology. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
