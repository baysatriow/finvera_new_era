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
     * LANDING PAGE — DAFTAR CICILAN BELUM LUNAS
     * ============================================================
     */
    public function index()
    {
        $installments = Installment::whereHas('loan', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', '!=', 'paid')
            ->with(['loan'])
            ->orderByRaw("FIELD(status, 'late', 'pending', 'waiting')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('installments.index', compact('installments'));
    }

    /**
     * ============================================================
     * PAYMENT PAGE — HALAMAN PEMBAYARAN CICILAN
     * ============================================================
     */
    public function showPaymentPage($id)
    {
        $installment = Installment::with('loan')
            ->findOrFail($id);

        /**
         * ------------------------------------------------------------
         * VALIDASI KEPEMILIKAN & STATUS
         * ------------------------------------------------------------
         */
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($installment->status === 'paid') {
            return redirect()
                ->route('installments.index')
                ->with('info', 'Cicilan ini sudah lunas.');
        }

        /**
         * ------------------------------------------------------------
         * AMBIL REKENING PERUSAHAAN AKTIF
         * ------------------------------------------------------------
         */
        $adminBanks = CompanyBankAccount::where('is_active', true)->get();

        return view('installments.pay', compact('installment', 'adminBanks'));
    }

    /**
     * ============================================================
     * SUBMIT PAYMENT — UPLOAD BUKTI PEMBAYARAN
     * ============================================================
     */
    public function submitPayment(Request $request, $id)
    {
        /**
         * ------------------------------------------------------------
         * VALIDASI INPUT
         * ------------------------------------------------------------
         */
        $request->validate([
            'proof_file' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'proof_file.required' => 'Bukti transfer wajib diunggah.',
            'proof_file.image'    => 'File harus berupa gambar.',
            'proof_file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $installment = Installment::findOrFail($id);

        /**
         * ------------------------------------------------------------
         * VALIDASI KEPEMILIKAN DATA
         * ------------------------------------------------------------
         */
        if ($installment->loan->user_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            /**
             * --------------------------------------------------------
             * PROSES UPLOAD FILE
             * --------------------------------------------------------
             */
            if ($request->hasFile('proof_file')) {
                $path = $request->file('proof_file')
                    ->store('payments', 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file bukti pembayaran.');
                }

                /**
                 * ----------------------------------------------------
                 * UPDATE DATA CICILAN
                 * ----------------------------------------------------
                 */
                $installment->update([
                    'status'      => 'waiting',
                    'proof_path'  => $path,
                    'paid_at'     => now(),
                ]);

                DB::commit();

                return redirect()
                    ->route('installments.index')
                    ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi Admin.');
            }

            return back()->with('error', 'File bukti pembayaran tidak ditemukan.');

        } catch (\Exception $e) {

            DB::rollBack();

            /**
             * --------------------------------------------------------
             * CLEANUP FILE JIKA GAGAL
             * --------------------------------------------------------
             */
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->with(
                'error',
                'Terjadi kesalahan sistem: ' . $e->getMessage()
            );
        }
    }
}
