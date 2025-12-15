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
     * LANDING PAGE — DAFTAR PINJAMAN & DISBURSEMENT
     * ============================================================
     */
    public function index()
    {
        $loans = Loan::with(['user', 'installments'])
            ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.disbursement.index', compact('loans'));
    }

    /**
     * ============================================================
     * DETAIL PAGE — DETAIL PINJAMAN & PROGRES CICILAN
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

        return view('admin.disbursement.show', compact(
            'loan',
            'paidAmount',
            'progress'
        ));
    }

    /**
     * ============================================================
     * VERIFY PAYMENT — VERIFIKASI PEMBAYARAN CICILAN USER
     * ============================================================
     */
    public function verifyPayment(Request $request, $installmentId)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($installmentId);

        /**
         * ========================================================
         * APPROVE PEMBAYARAN
         * ========================================================
         */
        if ($request->action === 'approve') {

            DB::transaction(function () use ($installment) {

                // 1. Update status cicilan
                $installment->update([
                    'status'      => 'paid',
                    'total_paid'  => $installment->amount + $installment->tazir_amount,
                ]);

                // 2. Update saldo pinjaman
                $loan = $installment->loan;
                $loan->remaining_balance -= $installment->amount;

                if ($loan->remaining_balance <= 100) {
                    $loan->remaining_balance = 0;
                    $loan->status = 'paid';
                }

                $loan->save();

                // 3. Kirim notifikasi ke user
                $loan->user->notify(new SystemNotification([
                    'title'   => 'Pembayaran Diterima',
                    'message' => 'Pembayaran cicilan ke-' . $installment->installment_number . ' telah diverifikasi.',
                    'type'    => 'success',
                    'url'     => route('installments.index'),
                ]));
            });

            return back()->with('success', 'Pembayaran berhasil diverifikasi.');
        }

        /**
         * ========================================================
         * REJECT PEMBAYARAN
         * ========================================================
         */
        $installment->update([
            'status'     => 'pending',
            'proof_path' => null,
            'paid_at'    => null,
        ]);

        // Kirim notifikasi penolakan
        $installment->loan->user->notify(new SystemNotification([
            'title'   => 'Pembayaran Ditolak',
            'message' => 'Bukti pembayaran cicilan ke-' . $installment->installment_number . ' tidak valid/buram. Silakan upload ulang.',
            'type'    => 'danger',
            'url'     => route('installments.index'),
        ]));

        return back()->with('error', 'Pembayaran ditolak. User diminta upload ulang.');
    }
}
