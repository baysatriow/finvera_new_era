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
     * INDEX — LIST CICILAN USER (AKTIF & RIWAYAT)
     * ============================================================ */
    public function index()
    {
        $installments = Installment::whereHas('loan', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['loan'])
            ->orderByRaw("FIELD(status, 'late', 'pending', 'paid')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('installments.index', compact('installments'));
    }

    /* ============================================================
     * PAY — PROSES PEMBAYARAN CICILAN
     * ============================================================ */
    public function pay(Request $request, $id)
    {
        $installment = Installment::with('loan')->findOrFail($id);

        // Validasi kepemilikan
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika sudah lunas
        if ($installment->status === 'paid') {
            return back()->with('info', 'Cicilan ini sudah lunas.');
        }

        // Harus bayar cicilan berurutan
        $hasPreviousUnpaid = Installment::where('loan_id', $installment->loan_id)
            ->where('installment_number', '<', $installment->installment_number)
            ->where('status', '!=', 'paid')
            ->exists();

        if ($hasPreviousUnpaid) {
            return back()->with('error', 'Harap lunasi cicilan bulan sebelumnya terlebih dahulu.');
        }

        DB::beginTransaction();

        try {
            /* ---------------------------
             * 1. Tandai cicilan sebagai lunas
             * --------------------------- */
            $installment->update([
                'status'     => 'paid',
                'paid_at'    => now(),
                'total_paid' => $installment->amount + $installment->tazir_amount,
            ]);

            /* ---------------------------
             * 2. Kurangi sisa hutang pada loan
             * --------------------------- */
            $loan = $installment->loan;
            $loan->remaining_balance -= $installment->amount;

            // Jika sisa hutang kecil, dianggap lunas
            if ($loan->remaining_balance <= 100) {
                $loan->remaining_balance = 0;
                $loan->status = 'paid';
            }

            $loan->save();

            DB::commit();

            /* ---------------------------
             * 3. Response UI
             * --------------------------- */
            if ($loan->status === 'paid') {
                return back()->with('success', 'Alhamdulillah! Seluruh pinjaman Anda telah lunas.');
            }

            return back()->with(
                'success',
                'Pembayaran cicilan ke-' . $installment->installment_number . ' berhasil diterima.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
