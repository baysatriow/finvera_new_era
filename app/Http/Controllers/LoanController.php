<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /* ============================================================
     * RIWAYAT PENGAJUAN PINJAMAN USER
     * ============================================================ */
    public function index()
    {
        $applications = LoanApplication::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('loans.index', compact('applications'));
    }

    /* ============================================================
     * HALAMAN FORM PENGAJUAN PINJAMAN
     * ============================================================ */
    public function create()
    {
        $user = Auth::user();

        // Pastikan user sudah KYC
        if ($user->kyc_status !== 'verified') {
            return redirect()->route('kyc.create')->with('warning', 'Verifikasi identitas dulu.');
        }

        // Pastikan user sudah daftar rekening bank
        if (!BankAccount::where('user_id', $user->id)->exists()) {
            return redirect()->route('bank.create')->with('warning', 'Daftarkan rekening dulu.');
        }

        // Cegah pengajuan jika masih ada pinjaman aktif atau pending
        $hasActive = LoanApplication::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActive) {
            return redirect()->route('dashboard')->with('error', 'Anda masih memiliki pinjaman aktif atau dalam proses.');
        }

        $product = LoanProduct::first();
        return view('loans.create', compact('product'));
    }

    /* ============================================================
     * SUBMIT PENGAJUAN PINJAMAN
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1000000',
            'tenor'          => 'required|integer|min:1|max:24',
            'purpose'        => 'required|string|min:10',
            'asset_type'     => 'required|string',
            'asset_value'    => 'required|numeric|min:0',
            'asset_document' => 'required|image|max:4096',
            'asset_selfie'   => 'required|image|max:4096',
            'tos_agreement'  => 'accepted',
        ]);

        $user = Auth::user();

        // Upload file jaminan
        $assetDocPath    = $request->file('asset_document')->store('assets', 'public');
        $assetSelfiePath = $request->file('asset_selfie')->store('assets', 'public');

        /* ------------------------------------------------------------
         * AI CREDIT SCORING
         * ------------------------------------------------------------ */
        $aiAnalysis = $this->geminiService->analyzeCreditProfile(
            $user,
            $request->amount,
            $request->tenor,
            $request->purpose,
            $request->asset_type
        );

        if (!$aiAnalysis) {
            // Fallback jika AI gagal
            $score     = 75;
            $status    = 'pending';
            $userMsg   = "Analisis AI tertunda, menunggu review manual.";
            $adminNote = "AI Service Timeout.";
        } else {
            $score     = $aiAnalysis['credit_score'];
            $status    = ($score >= 75) ? 'approved' : 'pending';
            $userMsg   = $aiAnalysis['user_message'];
            $adminNote = "Risk Analysis: " . $aiAnalysis['admin_analysis'];
        }

        /* ------------------------------------------------------------
         * SIMPAN PENGAJUAN PINJAMAN
         * ------------------------------------------------------------ */
        DB::beginTransaction();
        try {
            $finalPurpose = $request->purpose . " [Jaminan: {$request->asset_type}]";

            $application = LoanApplication::create([
                'user_id'            => $user->id,
                'loan_product_id'    => $request->loan_product_id,
                'amount'             => $request->amount,
                'tenor'              => $request->tenor,
                'purpose'            => $finalPurpose,
                'asset_document_path'=> $assetDocPath,
                'asset_selfie_path'  => $assetSelfiePath,
                'asset_value'        => $request->asset_value,
                'ai_score'           => $score,
                'ai_user_message'    => $userMsg,
                'status'             => $status,
                'admin_note'         => $adminNote,
                'reviewed_at'        => $status === 'approved' ? now() : null,
            ]);

            // Update credit score user
            $user->update(['credit_score' => $score]);

            // Jika langsung disetujui oleh AI → buat pinjaman aktif
            if ($status === 'approved') {
                $this->generateActiveLoan($application);
            }

            DB::commit();

            $msgType = $status === 'approved' ? 'success' : 'info';
            return redirect()
                ->route('history')
                ->with($msgType, "Pengajuan berhasil dikirim. Status: " . ucfirst($status));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /* ============================================================
     * GENERATE PINJAMAN AKTIF + CICILAN
     * ============================================================ */
    private function generateActiveLoan(LoanApplication $application)
    {
        $loan = Loan::create([
            'user_id'          => $application->user_id,
            'application_id'   => $application->id,
            'loan_code'        => 'LN-' . strtoupper(Str::random(8)),
            'total_amount'     => $application->amount,
            'remaining_balance'=> $application->amount,
            'status'           => 'active',
            'start_date'       => now(),
            'due_date'         => now()->addMonths($application->tenor),
            'disbursed_at'     => now(),
        ]);

        // Buat cicilan bulanan
        $monthlyAmount = ceil($application->amount / $application->tenor);

        for ($i = 1; $i <= $application->tenor; $i++) {
            Installment::create([
                'loan_id'           => $loan->id,
                'installment_number'=> $i,
                'due_date'          => now()->addMonths($i),
                'amount'            => $monthlyAmount,
                'status'            => 'pending',
            ]);
        }
    }

    /* ============================================================
     * DETAIL PENGAJUAN PINJAMAN USER
     * ============================================================ */
    public function show(LoanApplication $loan)
    {
        if ($loan->user_id !== Auth::id()) {
            abort(403);
        }

        $loan->load('loan.installments');
        return view('loans.show', compact('loan'));
    }
}
