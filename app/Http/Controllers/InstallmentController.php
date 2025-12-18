<?php

namespace App\Http\Controllers;

use App\Models\CompanyBankAccount;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InstallmentController extends Controller
{
    /**
     * ============================================================
     * INDEX — DAFTAR CICILAN USER
     * ============================================================
     */
    public function index()
    {
        $installments = Installment::whereHas('loan', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', '!=', 'paid')
            ->with(['loan'])
            // Prioritas: Failed → Late → Waiting → Pending
            ->orderByRaw("FIELD(status, 'failed', 'late', 'waiting', 'pending')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('installments.index', compact('installments'));
    }

    /**
     * ============================================================
     * SHOW — HALAMAN PEMBAYARAN CICILAN
     * ============================================================
     */
    public function showPaymentPage($id)
    {
        $installment = Installment::with('loan')
            ->findOrFail($id);

        /**
         * --------------------------------------------------------
         * AUTHORIZATION
         * --------------------------------------------------------
         */
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        /**
         * --------------------------------------------------------
         * VALIDASI STATUS
         * --------------------------------------------------------
         */
        if ($installment->status === 'paid') {
            return redirect()
                ->route('installments.index')
                ->with('info', 'Cicilan ini sudah lunas.');
        }

        /**
         * --------------------------------------------------------
         * DATA REKENING PERUSAHAAN
         * --------------------------------------------------------
         */
        $adminBanks = CompanyBankAccount::where('is_active', true)->get();

        return view('installments.pay', compact('installment', 'adminBanks'));
    }

    /**
     * ============================================================
     * STORE — SUBMIT / UPLOAD BUKTI PEMBAYARAN
     * ============================================================
     */
    public function submitPayment(Request $request, $id)
    {
        /**
         * --------------------------------------------------------
         * VALIDASI INPUT
         * --------------------------------------------------------
         */
        $request->validate([
            'proof_file' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $installment = Installment::findOrFail($id);

        /**
         * --------------------------------------------------------
         * AUTHORIZATION
         * --------------------------------------------------------
         */
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {

            /**
             * --------------------------------------------------------
             * UPLOAD & SIMPAN BUKTI PEMBAYARAN
             * --------------------------------------------------------
             */
            if ($request->hasFile('proof_file')) {

                // Hapus file lama (jika sebelumnya ditolak)
                if ($installment->proof_path) {
                    Storage::disk('public')->delete($installment->proof_path);
                }

                $path = $request->file('proof_file')
                    ->store('payments', 'public');

                /**
                 * ----------------------------------------------------
                 * UPDATE STATUS CICILAN
                 * ----------------------------------------------------
                 */
                $installment->update([
                    'status'             => 'waiting',
                    'proof_path'         => $path,
                    'paid_at'            => now(),
                    'rejection_reason'   => null,
                ]);

                DB::commit();

                return redirect()
                    ->route('installments.index')
                    ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi Admin.');
            }

            return back()->with('error', 'File bukti pembayaran wajib diunggah.');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with(
                'error',
                'Gagal mengunggah bukti pembayaran: ' . $e->getMessage()
            );
        }
    }
}
