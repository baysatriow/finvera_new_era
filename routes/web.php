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
use Illuminate\Support\Facades\Route;

/**
 * ============================================================
 * LANDING PAGE
 * ============================================================
 */
Route::get('/', [LandingController::class, 'index'])->name('landing');

/**
 * ============================================================
 * GUEST ROUTES — HANYA UNTUK USER BELUM LOGIN
 * ============================================================
 */
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterStep1'])->name('register');
    Route::post('/register',[AuthController::class, 'storeRegisterStep1']);
});

/**
 * ============================================================
 * AUTH ROUTES — HANYA UNTUK USER YANG SUDAH LOGIN
 * ============================================================
 */
Route::middleware('auth')->group(function () {

    /**
     * ==========================
     * LOGOUT
     * ==========================
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * ==========================
     * REGISTER STEP 2
     * ==========================
     */
    Route::get('/register/step-2', [AuthController::class, 'showRegisterStep2'])->name('register.step2');
    Route::post('/register/step-2', [AuthController::class, 'storeRegisterStep2']);

    /**
     * ==========================
     * DASHBOARD
     * ==========================
     */
    Route::get('/dashboard',            [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ai-advice',  [DashboardController::class, 'getAiAdvice'])->name('dashboard.ai-advice');

    /**
     * ==========================
     * PROFIL PENGGUNA
     * ==========================
     */
    Route::get('/profile',              [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',              [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');

    /**
     * ==========================
     * KYC VERIFICATION
     * ==========================
     */
    Route::get('/kyc/verify',           [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc/verify',          [KycController::class, 'store'])->name('kyc.store');

    /**
     * ==========================
     * REKENING BANK
     * ==========================
     */
    Route::resource('bank', BankAccountController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('/bank/{bank}/edit',     [BankAccountController::class, 'edit'])->name('bank.edit');
    Route::put('/bank/{bank}',          [BankAccountController::class, 'update'])->name('bank.update');
    Route::post('/bank/{id}/primary',   [BankAccountController::class, 'setPrimary'])->name('bank.primary');

    /**
     * ==========================
     * PINJAMAN — BORROWER
     * ==========================
     */
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/history', [LoanController::class, 'index'])->name('history');

    /**
     * ==========================
     * CICILAN
     * ==========================
     */
    Route::get('/installments',            [InstallmentController::class, 'index'])->name('installments.index');
    Route::post('/installments/{id}/pay',  [InstallmentController::class, 'pay'])->name('installments.pay');

    /**
     * ==========================
     * NOTIFIKASI USER
     * ==========================
     */
    Route::get('/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}',       [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/notifications/{id}/read',  [NotificationController::class, 'show'])->name('notifications.read');

    /**
     * ============================================================
     * ADMIN ROUTES
     * ============================================================
     */
    Route::prefix('admin')->name('admin.')->group(function () {

        /**
         * ==========================
         * DASHBOARD ADMIN
         * ==========================
         */
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        /**
         * ==========================
         * APPROVAL PINJAMAN
         * ==========================
         */
        Route::get('/applications',              [AdminController::class, 'applications'])->name('applications');
        Route::get('/applications/{id}',         [AdminController::class, 'showApplication'])->name('applications.show');
        Route::post('/applications/{id}/approve',[AdminController::class, 'approve'])->name('approve');
        Route::post('/applications/{id}/reject', [AdminController::class, 'reject'])->name('reject');

        /**
         * ==========================
         * DATA PEMINJAM
         * ==========================
         */
        Route::resource('borrowers', AdminBorrowerController::class)->only(['index', 'show']);

        /**
         * ==========================
         * MANAJEMEN USER
         * ==========================
         */
        Route::resource('users', AdminUserController::class);

        /**
         * ==========================
         * DISBURSEMENT & CICILAN
         * ==========================
         */
        Route::resource('disbursement', AdminDisbursementController::class)->only(['index', 'show']);

        /**
         * ==========================
         * LAPORAN & ANALITIK
         * ==========================
         */
        Route::get('/reports',        [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

        /**
         * ==========================
         * NOTIFIKASI ADMIN
         * ==========================
         */
        Route::get('/notifications',        [App\Http\Controllers\AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/send',  [App\Http\Controllers\AdminNotificationController::class, 'store'])->name('notifications.send');
    });
});
