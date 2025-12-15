<?php

use App\Http\Controllers\AdminBorrowerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDisbursementController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminBankController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPaymentController;

// --- HALAMAN UTAMA (LANDING PAGE) ---
Route::get('/', [LandingController::class, 'index'])->name('landing');

// --- GUEST ROUTES (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterStep1'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegisterStep1']);
});

// --- AUTH ROUTES (Sudah Login) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- REGISTRASI TAHAP 2 (DATA PRIBADI) ---
    Route::get('/register/step-2', [AuthController::class, 'showRegisterStep2'])->name('register.step2');
    Route::post('/register/step-2', [AuthController::class, 'storeRegisterStep2']);

    // --- DASHBOARD & UTAMA ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ai-advice', [DashboardController::class, 'getAiAdvice'])->name('dashboard.ai-advice');

    // --- PROFIL PENGGUNA ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- VERIFIKASI IDENTITAS (KYC) ---
    Route::get('/kyc/verify', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc/verify', [KycController::class, 'store'])->name('kyc.store');

    // --- REKENING BANK ---
    Route::resource('bank', BankAccountController::class)->except(['show']);
    Route::post('/bank/{id}/primary', [BankAccountController::class, 'setPrimary'])->name('bank.primary');

    // --- PINJAMAN (BORROWER) ---
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/history', [LoanController::class, 'index'])->name('history');

    // --- CICILAN & PEMBAYARAN ---
    Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
    // Halaman Upload Bukti (Menggantikan tombol bayar instan)
    Route::get('/installments/{id}/pay', [InstallmentController::class, 'showPaymentPage'])->name('installments.pay');
    // Proses Upload Bukti
    Route::post('/installments/{id}/submit', [InstallmentController::class, 'submitPayment'])->name('installments.submit');

    // --- NOTIFIKASI USER ---
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    // Alias route untuk compatibility jika ada view lama yang memanggil .read
    Route::get('/notifications/{id}/read', [NotificationController::class, 'show'])->name('notifications.read');

    // --- AREA ADMIN ---
    Route::prefix('admin')->name('admin.')->group(function() {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        // KONFIRMASI PEMBAYARAN
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        // Persetujuan Pinjaman (Approval)
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
        Route::get('/applications/{id}', [AdminController::class, 'showApplication'])->name('applications.show');
        Route::post('/applications/{id}/approve', [AdminController::class, 'approve'])->name('approve');
        Route::post('/applications/{id}/reject', [AdminController::class, 'reject'])->name('reject');

        // Data Peminjam
        Route::resource('borrowers', AdminBorrowerController::class)->only(['index', 'show']);

        // Manajemen User (Admin & Staff)
        Route::resource('users', AdminUserController::class);

        // Manajemen Disbursement (Pencairan & Verifikasi Bayar)
        Route::resource('disbursement', AdminDisbursementController::class)->only(['index', 'show']);
        // Route Verifikasi Pembayaran Cicilan
        Route::post('/disbursement/verify/{id}', [AdminDisbursementController::class, 'verifyPayment'])->name('disbursement.verify');

        // Rekening Perusahaan (Admin Bank)
        Route::resource('banks', AdminBankController::class);
        Route::post('/banks/{id}/primary', [AdminBankController::class, 'setPrimary'])->name('banks.primary');
        // Laporan & Analitik
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

        // Kelola Notifikasi (Broadcast)
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/send', [AdminNotificationController::class, 'store'])->name('notifications.send');
    });
});
