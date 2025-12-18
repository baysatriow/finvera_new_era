<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDisbursementController extends Controller
{
    /**
     * ============================================================
     * INDEX — DAFTAR PINJAMAN & PENCAIRAN
     * ============================================================
     */
    public function index()
    {
        // Urutkan:
        // - Belum lunas (active, past_due, default) di atas
        // - Lunas (paid) di bawah
        // - Terbaru lebih dulu
        $loans = Loan::with(['user', 'installments'])
            ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->orderByRaw("CASE WHEN status = 'paid' THEN 2 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.disbursement.index', compact('loans'));
    }

    /**
     * ============================================================
     * SHOW — DETAIL PINJAMAN
     * ============================================================
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'application', 'installments'])
            ->findOrFail($id);

        $paidAmount = $loan->total_amount - $loan->remaining_balance;
        $progress   = ($loan->total_amount > 0)
            ? ($paidAmount / $loan->total_amount) * 100
            : 0;

        return view(
            'admin.disbursement.show',
            compact('loan', 'paidAmount', 'progress')
        );
    }

    /**
     * ============================================================
     * VERIFY PAYMENT — VERIFIKASI CICILAN
     * ============================================================
     */
    public function verifyPayment(Request $request, $installmentId)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($installmentId);

        /**
         * ------------------------------------------------------------
         * APPROVE — TERIMA PEMBAYARAN
         * ------------------------------------------------------------
         */
        if ($request->action === 'approve') {

            DB::transaction(function () use ($installment) {

                // 1. Update data cicilan
                $installment->update([
                    'status'          => 'paid',
                    'total_paid'      => $installment->amount + $installment->tazir_amount,
                    'rejection_reason'=> null,
                ]);

                // 2. Update saldo pinjaman
                $loan = $installment->loan;
                $loan->remaining_balance -= $installment->amount;

                if ($loan->remaining_balance <= 100) {
                    $loan->remaining_balance = 0;
                    $loan->status = 'paid';

                    // Notifikasi pinjaman lunas
                    $loan->user->notify(new SystemNotification([
                        'title'   => 'Pinjaman Lunas!',
                        'message' => 'Selamat! Seluruh pinjaman Anda telah lunas.',
                        'type'    => 'success',
                        'url'     => route('history'),
                    ]));
                }

                $loan->save();

                // 3. Notifikasi pembayaran diterima
                $installment->loan->user->notify(new SystemNotification([
                    'title'   => 'Pembayaran Diterima',
                    'message' => 'Pembayaran cicilan bulan ke-' . $installment->installment_number . ' telah diverifikasi.',
                    'type'    => 'success',
                    'url'     => route('installments.index'),
                ]));
            });

            return back()->with('success', 'Pembayaran diverifikasi dan diterima.');
        }

        /**
         * ------------------------------------------------------------
         * REJECT — TOLAK PEMBAYARAN
         * ------------------------------------------------------------
         */
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $installment->update([
            'status'            => 'failed',
            'rejection_reason'  => $request->reason,
        ]);

        // Notifikasi pembayaran ditolak
        $installment->loan->user->notify(new SystemNotification([
            'title'   => 'Pembayaran Ditolak',
            'message' => 'Bukti pembayaran ditolak. Alasan: ' . $request->reason,
            'type'    => 'danger',
            'url'     => route('installments.index'),
        ]));

        return back()->with('success', 'Pembayaran ditolak. Status diubah menjadi Gagal.');
    }
}
