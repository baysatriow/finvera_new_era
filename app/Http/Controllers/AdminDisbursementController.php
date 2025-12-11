<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class AdminDisbursementController extends Controller
{
    /* ============================================================
     * LIST PINJAMAN YANG TELAH DICAIKAN
     * ============================================================ */
    public function index()
    {
        // Ambil semua pinjaman yang sudah dicairkan (bukan pending / rejected)
        $loans = Loan::with(['user', 'installments'])
            ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.disbursement.index', compact('loans'));
    }

    /* ============================================================
     * DETAIL PINJAMAN & JADWAL CICILAN
     * ============================================================ */
    public function show($id)
    {
        $loan = Loan::with(['user', 'application', 'installments'])
            ->findOrFail($id);

        // Hitung jumlah yang sudah dibayarkan + progress persentase
        $paidAmount = $loan->total_amount - $loan->remaining_balance;
        $progress   = $loan->total_amount > 0
            ? ($paidAmount / $loan->total_amount) * 100
            : 0;

        return view('admin.disbursement.show', compact(
            'loan',
            'paidAmount',
            'progress'
        ));
    }
}
