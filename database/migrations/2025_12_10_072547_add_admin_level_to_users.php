<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminBorrowerController extends Controller
{
    /**
     * Daftar seluruh peminjam.
     */
    public function index()
    {
        // Ambil user dengan role 'borrower', eager load KYC untuk efisiensi
        $borrowers = User::where('role', 'borrower')
            ->with('kyc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.borrowers.index', compact('borrowers'));
    }

    /**
     * Detail lengkap peminjam.
     */
    public function show($id)
    {
        // Ambil data user beserta relasi penting: KYC, Bank, Aplikasi (History)
        $borrower = User::with(['kyc', 'bankAccounts', 'applications' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->where('role', 'borrower')
            ->findOrFail($id);

        return view('admin.borrowers.show', compact('borrower'));
    }
}
