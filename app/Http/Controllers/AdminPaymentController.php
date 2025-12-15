<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    /**
     * ============================================================
     * LANDING PAGE — DAFTAR PEMBAYARAN MENUNGGU VERIFIKASI
     * ============================================================
     */
    public function index()
    {
        $payments = Installment::with(['loan.user', 'loan.application'])
            ->where('status', 'waiting')
            ->orderBy('paid_at', 'asc')
            ->get();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * ============================================================
     * DETAIL PAGE — DETAIL PEMBAYARAN CICILAN
     * ============================================================
     */
    public function show($id)
    {
        $payment = Installment::with(['loan.user', 'loan.application'])
            ->findOrFail($id);

        // Validasi status (keamanan tambahan)
        if ($payment->status !== 'waiting') {
            return redirect()
                ->route('admin.payments.index')
                ->with('info', 'Pembayaran ini sudah diproses.');
        }

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * ============================================================
     * APPROVE PAYMENT — SETUJUI PEMBAYARAN CICILAN
     * ============================================================
     */
    public function approve($id)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($id);

        // Cegah double processing
        if ($installment->status !== 'waiting') {
            return back()->with('error', 'Data sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($installment) {

            /**
             * --------------------------------------------------------
             * 1. UPDATE STATUS CICILAN
             * --------------------------------------------------------
             */
            $installment->update([
                'status'      => 'paid',
                'total_paid'  => $installment->amount + $installment->tazir_amount,
            ]);

            /**
             * --------------------------------------------------------
             * 2. UPDATE SALDO PINJAMAN
             * --------------------------------------------------------
             */
            $loan = $installment->loan;
            $loan->remaining_balance -= $installment->amount;

            // Cek pelunasan
            if ($loan->remaining_balance <= 100) {
                $loan->remaining_balance = 0;
                $loan->status = 'paid';

                // Notifikasi pinjaman lunas
                $loan->user->notify(new SystemNotification([
                    'title'   => 'Pinjaman Lunas!',
                    'message' => 'Selamat! Seluruh kewajiban pinjaman Anda telah lunas.',
                    'type'    => 'success',
                    'url'     => route('history'),
                ]));
            }

            $loan->save();

            /**
             * --------------------------------------------------------
             * 3. NOTIFIKASI PEMBAYARAN BERHASIL
             * --------------------------------------------------------
             */
            $loan->user->notify(new SystemNotification([
                'title'   => 'Pembayaran Diterima',
                'message' => 'Pembayaran cicilan bulan ke-' . $installment->installment_number . ' telah diverifikasi.',
                'type'    => 'success',
                'url'     => route('installments.index'),
            ]));
        });

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    /**
     * ============================================================
     * REJECT PAYMENT — TOLAK PEMBAYARAN CICILAN
     * ============================================================
     */
    public function reject(Request $request, $id)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($id);

        $request->validate([
            'reason' => 'required|string',
        ]);

        /**
         * --------------------------------------------------------
         * RESET DATA PEMBAYARAN
         * --------------------------------------------------------
         */
        $installment->update([
            'status'     => 'pending',
            'proof_path'=> null,
            'paid_at'   => null,
        ]);

        /**
         * --------------------------------------------------------
         * NOTIFIKASI PENOLAKAN
         * --------------------------------------------------------
         */
        $installment->loan->user->notify(new SystemNotification([
            'title'   => 'Bukti Pembayaran Ditolak',
            'message' => 'Bukti pembayaran cicilan ke-' .
                $installment->installment_number .
                ' ditolak. Alasan: ' . $request->reason,
            'type'    => 'danger',
            'url'     => route('installments.pay', $installment->id),
        ]));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pembayaran ditolak. User diminta upload ulang.');
    }
}
