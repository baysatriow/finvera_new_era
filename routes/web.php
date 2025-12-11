<?php

use App\Http\Controllers\AdminBorrowerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDisbursementController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController; // Pastikan controller ini di-import
use App\Http\Controllers\KycController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman Utama (Landing Page)
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

    // Register Step 2 (Data Diri)
    Route::get('/register/step-2', [AuthController::class, 'showRegisterStep2'])->name('register.step2');
    Route::post('/register/step-2', [AuthController::class, 'storeRegisterStep2']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ai-advice', [DashboardController::class, 'getAiAdvice'])->name('dashboard.ai-advice');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Verifikasi KYC
    Route::get('/kyc/verify', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc/verify', [KycController::class, 'store'])->name('kyc.store');

    // Rekening Bank
    Route::resource('bank', BankAccountController::class)->only(['index', 'create', 'store', 'destroy']);

    // Pinjaman (Borrower)
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/history', [LoanController::class, 'index'])->name('history');

    // --- FIX: Rute Pembayaran Cicilan ---
    Route::post('/installments/{id}/pay', [InstallmentController::class, 'pay'])->name('installments.pay');

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->group(function() {
        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Persetujuan Pinjaman (Approval)
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
        Route::post('/applications/{id}/approve', [AdminController::class, 'approve'])->name('approve');
        Route::post('/applications/{id}/reject', [AdminController::class, 'reject'])->name('reject');

        // Data Peminjam (Borrower List)
        Route::resource('borrowers', AdminBorrowerController::class)->only(['index', 'show']);

        // Manajemen User (Admin & Staff)
        Route::resource('users', AdminUserController::class);

        // Manajemen Disbursement & Pembayaran (Cicilan)
        Route::resource('disbursement', AdminDisbursementController::class)->only(['index', 'show']);

        // Laporan & Analitik
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
    });
});
