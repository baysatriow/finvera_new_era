<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminBorrowerController extends Controller
{
    /* ============================================================
     * INDEX — LIST SEMUA PEMINJAM
     * ============================================================ */
    public function index()
    {
        // Ambil semua user dengan role borrower + relasi KYC
        $borrowers = User::where('role', 'borrower')
            ->with('kyc')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.borrowers.index', compact('borrowers'));
    }

    /* ============================================================
     * SHOW — DETAIL LENGKAP PEMINJAM
     * ============================================================ */
    public function show($id)
    {
        // Ambil borrower + relasi yang diperlukan: kyc, bank, aplikasi pinjaman
        $borrower = User::with([
                'kyc',
                'bankAccounts',
                'applications' => fn ($query) => $query->orderByDesc('created_at')
            ])
            ->where('role', 'borrower')
            ->findOrFail($id);

        return view('admin.borrowers.show', compact('borrower'));
    }
}
