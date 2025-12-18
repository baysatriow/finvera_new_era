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
     * INDEX — DAFTAR PEMBAYARAN MENUNGGU VERIFIKASI
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
     * SHOW — DETAIL PEMBAYARAN
     * ============================================================
     */
    public function show($id)
    {
        $payment = Installment::with(['loan.user', 'loan.application'])
            ->findOrFail($id);

        // Optional security: hanya status waiting
        if ($payment->status !== 'waiting') {
            return redirect()
                ->route('admin.payments.index')
                ->with('info', 'Pembayaran ini sudah diproses.');
        }

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * ============================================================
     * APPROVE — SETUJUI PEMBAYARAN
     * ============================================================
     */
    public function approve($id)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($id);

        if ($installment->status !== 'waiting') {
            return back()->with('error', 'Data sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($installment) {

            /**
             * --------------------------------------------------------
             * 1. UPDATE DATA CICILAN
             * --------------------------------------------------------
             */
            $installment->update([
                'status'             => 'paid',
                'total_paid'         => $installment->amount + $installment->tazir_amount,
                'rejection_reason'   => null,
                // paid_at sudah diisi saat user upload
            ]);

            /**
             * --------------------------------------------------------
             * 2. UPDATE SALDO PINJAMAN
             * --------------------------------------------------------
             */
            $loan = $installment->loan;
            $loan->remaining_balance -= $installment->amount;

            // Cek lunas total
            if ($loan->remaining_balance <= 100) {
                $loan->remaining_balance = 0;
                $loan->status = 'paid';

                // Notifikasi lunas
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
             * 3. NOTIFIKASI PEMBAYARAN DITERIMA
             * --------------------------------------------------------
             */
            $loan->user->notify(new SystemNotification([
                'title'   => 'Pembayaran Diterima',
                'message' => 'Pembayaran cicilan bulan ke-' . $installment->installment_number . ' telah diverifikasi dan diterima.',
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
     * REJECT — TOLAK PEMBAYARAN
     * ============================================================
     */
    public function reject(Request $request, $id)
    {
        $installment = Installment::with('loan.user')
            ->findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        /**
         * --------------------------------------------------------
         * UPDATE STATUS GAGAL
         * --------------------------------------------------------
         * Proof path TIDAK dihapus agar user bisa melihat bukti
         */
        $installment->update([
            'status'            => 'failed',
            'rejection_reason'  => $request->reason,
        ]);

        /**
         * --------------------------------------------------------
         * NOTIFIKASI PENOLAKAN
         * --------------------------------------------------------
         */
        $installment->loan->user->notify(new SystemNotification([
            'title'   => 'Pembayaran Ditolak',
            'message' => 'Bukti pembayaran cicilan ke-' . $installment->installment_number .
                         ' ditolak. Alasan: ' . $request->reason,
            'type'    => 'danger',
            'url'     => route('installments.index'),
        ]));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pembayaran ditolak. User diminta upload ulang.');
    }
}
