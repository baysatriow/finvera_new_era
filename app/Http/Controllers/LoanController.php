<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Notifications\SystemNotification;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    protected $geminiService;

    /**
     * ============================================================
     * CONSTRUCTOR — INJECT AI SERVICE
     * ============================================================
     */
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * ============================================================
     * LANDING PAGE — RIWAYAT PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function index()
    {
        $applications = LoanApplication::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('loans.index', compact('applications'));
    }

    /**
     * ============================================================
     * CREATE PAGE — FORM PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function create()
    {
        $user = Auth::user();

        /**
         * ------------------------------------------------------------
         * VALIDASI KYC (WAJIB VERIFIED)
         * ------------------------------------------------------------
         */
        if ($user->kyc_status !== 'verified') {
            return redirect()
                ->route('kyc.create')
                ->with('warning', 'Silakan verifikasi identitas (KYC) terlebih dahulu sebelum mengajukan pinjaman.');
        }

        /**
         * ------------------------------------------------------------
         * VALIDASI REKENING BANK PENCAIRAN
         * ------------------------------------------------------------
         */
        if (!BankAccount::where('user_id', $user->id)->exists()) {
            return redirect()
                ->route('bank.create')
                ->with('warning', 'Anda wajib mendaftarkan rekening bank pencairan sebelum mengajukan pinjaman.');
        }

        /**
         * ------------------------------------------------------------
         * CEK PENGAJUAN MASIH PENDING
         * ------------------------------------------------------------
         */
        $hasPendingApp = LoanApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApp) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Anda masih memiliki pengajuan yang sedang diproses. Mohon tunggu keputusan admin.');
        }

        /**
         * ------------------------------------------------------------
         * CEK PINJAMAN AKTIF BELUM LUNAS
         * ------------------------------------------------------------
         */
        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->whereIn('status', ['active', 'past_due', 'default'])
            ->exists();

        if ($hasActiveLoan) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda masih memiliki pinjaman aktif yang belum lunas. Silakan lunasi terlebih dahulu.');
        }

        /**
         * ------------------------------------------------------------
         * LOAD PRODUK PINJAMAN
         * ------------------------------------------------------------
         */
        $product = LoanProduct::first();

        return view('loans.create', compact('product'));
    }

    /**
     * ============================================================
     * STORE — PROSES PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function store(Request $request)
    {
        /**
         * ------------------------------------------------------------
         * VALIDASI INPUT FORM
         * ------------------------------------------------------------
         */
        $request->validate([
            'amount'          => 'required|numeric|min:1000000',
            'tenor'           => 'required|integer|min:1|max:24',
            'purpose'         => 'required|string|min:10',
            'asset_type'      => 'required|string',
            'asset_value'     => 'required|numeric|min:0',
            'asset_document'  => 'required|image|mimes:jpeg,png,jpg,pdf|max:4096',
            'asset_selfie'    => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'tos_agreement'   => 'accepted',
        ]);

        $user = Auth::user();

        /**
         * ------------------------------------------------------------
         * UPLOAD DOKUMEN ASET
         * ------------------------------------------------------------
         */
        $assetDocPath = $request->file('asset_document')
            ->store('assets', 'public');

        $assetSelfiePath = $request->file('asset_selfie')
            ->store('assets', 'public');

        /**
         * ------------------------------------------------------------
         * ANALISIS KREDIT MENGGUNAKAN AI (GEMINI)
         * ------------------------------------------------------------
         */
        $aiAnalysis = $this->geminiService->analyzeCreditProfile(
            $user,
            $request->amount,
            $request->tenor,
            $request->purpose,
            $request->asset_type
        );

        /**
         * ------------------------------------------------------------
         * SET STATUS DEFAULT
         * ------------------------------------------------------------
         */
        $status = 'pending';

        if (!$aiAnalysis || isset($aiAnalysis['error'])) {

            $score     = 75;
            $userMsg  = 'Analisis AI tertunda. Pengajuan Anda tetap masuk dan akan direview manual oleh Admin.';
            $errorLog = $aiAnalysis['error'] ?? 'Unknown error (Null response)';
            $adminNote = 'AI Service Error: ' . $errorLog;

        } else {

            $score = $aiAnalysis['credit_score'];

            if ($score >= 75) {
                $userMsg   = $aiAnalysis['user_message'];
                $adminNote = 'Risk Analysis: ' . $aiAnalysis['admin_analysis']
                    . ' [AI RECOMMENDATION: HIGHLY RECOMMENDED TO APPROVE]';
            } else {
                $userMsg   = $aiAnalysis['user_message'];
                $adminNote = 'Risk Analysis: ' . $aiAnalysis['admin_analysis'];
            }
        }

        DB::beginTransaction();

        try {
            /**
             * --------------------------------------------------------
             * SIMPAN DATA PENGAJUAN
             * --------------------------------------------------------
             */
            $application = LoanApplication::create([
                'user_id'              => $user->id,
                'loan_product_id'      => $request->loan_product_id,
                'amount'               => $request->amount,
                'tenor'                => $request->tenor,
                'purpose'              => $request->purpose,
                'asset_type'           => $request->asset_type,
                'asset_document_path'  => $assetDocPath,
                'asset_selfie_path'    => $assetSelfiePath,
                'asset_value'          => $request->asset_value,
                'ai_score'             => $score,
                'ai_user_message'      => $userMsg,
                'status'               => $status,
                'admin_note'           => $adminNote,
                'reviewed_at'          => null,
            ]);

            /**
             * --------------------------------------------------------
             * UPDATE CREDIT SCORE USER
             * --------------------------------------------------------
             */
            $user->update([
                'credit_score' => $score,
            ]);

            /**
             * --------------------------------------------------------
             * NOTIFIKASI KE USER
             * --------------------------------------------------------
             */
            $user->notify(new SystemNotification([
                'title'   => 'Pengajuan Diterima',
                'message' => 'Pengajuan pinjaman Anda sebesar Rp '
                    . number_format($request->amount, 0, ',', '.')
                    . ' telah diterima dan sedang direview.',
                'type'    => 'info',
                'url'     => route('loans.show', $application->id),
            ]));

            DB::commit();

            /**
             * --------------------------------------------------------
             * DATA POPUP SWEETALERT
             * --------------------------------------------------------
             */
            $popupData = [
                'title'   => 'Analisis AI Selesai',
                'score'   => $score,
                'message' => $userMsg,
                'status'  => $status,
            ];

            return redirect()
                ->route('loans.show', $application->id)
                ->with('success', 'Pengajuan berhasil dikirim.')
                ->with('ai_analysis_popup', $popupData);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan sistem: ' . $e->getMessage()
            );
        }
    }

    /**
     * ============================================================
     * DETAIL PAGE — DETAIL PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function show(LoanApplication $loan)
    {
        if ($loan->user_id !== Auth::id()) {
            abort(403);
        }

        $loan->load('loan.installments');

        return view('loans.show', compact('loan'));
    }
}
