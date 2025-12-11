<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    /* ============================================================
     * PROSES PEMBAYARAN CICILAN
     * ============================================================ */
    public function pay(Request $request, $id)
    {
        $installment = Installment::with('loan')->findOrFail($id);

        /* ------------------------------------------------------------
         * 1. VALIDASI KEPEMILIKAN
         * ------------------------------------------------------------ */
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        /* ------------------------------------------------------------
         * 2. CICILAN SUDAH LUNAS?
         * ------------------------------------------------------------ */
        if ($installment->status === 'paid') {
            return back()->with('info', 'Cicilan ini sudah lunas.');
        }

        /* ------------------------------------------------------------
         * 3. CEK CICILAN SEBELUMNYA (Harus bayar berurutan)
         * ------------------------------------------------------------ */
        $previousUnpaid = Installment::where('loan_id', $installment->loan_id)
            ->where('installment_number', '<', $installment->installment_number)
            ->where('status', '!=', 'paid')
            ->exists();

        if ($previousUnpaid) {
            return back()->with('error', 'Harap lunasi cicilan bulan sebelumnya terlebih dahulu.');
        }

        /* ------------------------------------------------------------
         * 4. PROSES TRANSAKSI PEMBAYARAN
         * ------------------------------------------------------------ */
        DB::beginTransaction();
        try {
            // A. Tandai cicilan sebagai lunas
            $installment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'total_paid' => $installment->amount + $installment->tazir_amount,
            ]);

            // B. Kurangi sisa hutang loan utama
            $loan = $installment->loan;
            $loan->remaining_balance -= $installment->amount;

            // C. Jika hampir nol, anggap lunas total (toleransi < 100 rupiah)
            if ($loan->remaining_balance <= 100) {
                $loan->remaining_balance = 0;
                $loan->status = 'paid';
            }

            $loan->save();
            DB::commit();

            /* ------------------------------------------------------------
             * 5. RESPONSE SUKSES
             * ------------------------------------------------------------ */
            if ($loan->status === 'paid') {
                return back()->with('success', 'Alhamdulillah! Seluruh pinjaman Anda telah lunas.');
            }

            return back()->with(
                'success',
                'Pembayaran cicilan ke-' . $installment->installment_number . ' berhasil diterima.'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
