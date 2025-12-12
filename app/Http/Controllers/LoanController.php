<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Notifications\SystemNotification;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    protected $geminiService;

    /**
     * ============================================================
     * CONSTRUCT — INIT GEMINI SERVICE
     * ============================================================
     */
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * ============================================================
     * INDEX — LIST PENGAJUAN PINJAMAN USER
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
     * CREATE — FORM PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->kyc_status !== 'verified') {
            return redirect()->route('kyc.create')->with('warning', 'Verifikasi identitas dulu.');
        }

        if (!BankAccount::where('user_id', $user->id)->exists()) {
            return redirect()->route('bank.create')->with('warning', 'Daftarkan rekening dulu.');
        }

        if (
            LoanApplication::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists()
        ) {
            return redirect()->route('dashboard')->with('error', 'Anda masih memiliki pinjaman aktif atau dalam proses.');
        }

        $product = LoanProduct::first();
        return view('loans.create', compact('product'));
    }

    /**
     * ============================================================
     * STORE — PROSES SUBMIT PENGAJUAN PINJAMAN
     * ============================================================
     */
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
        $assetDocPath    = $request->file('asset_document')->store('assets', 'public');
        $assetSelfiePath = $request->file('asset_selfie')->store('assets', 'public');

        // --- AI SCORING ---
        $aiAnalysis = $this->geminiService->analyzeCreditProfile(
            $user,
            $request->amount,
            $request->tenor,
            $request->purpose,
            $request->asset_type
        );

        $status = 'pending';

        if (!$aiAnalysis) {
            $score    = 75;
            $userMsg  = "Analisis AI tertunda, menunggu review manual.";
            $adminNote = "AI Service Timeout.";
        } else {
            $score = $aiAnalysis['credit_score'];
            $userMsg = $aiAnalysis['user_message'];
            $adminNote = "Risk Analysis: {$aiAnalysis['admin_analysis']}";

            if ($score >= 75) {
                $adminNote .= " [AI RECOMMENDATION: HIGHLY RECOMMENDED TO APPROVE]";
            }
        }

        DB::beginTransaction();
        try {
            $finalPurpose = $request->purpose . " [Jaminan: " . $request->asset_type . "]";

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
                'reviewed_at'        => null,
            ]);

            $user->update(['credit_score' => $score]);

            // --- NOTIFIKASI PENGAJUAN DITERIMA ---
            $user->notify(new SystemNotification([
                'title'   => 'Pengajuan Diterima',
                'message' => 'Pengajuan pinjaman Anda sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' telah diterima dan sedang direview.',
                'type'    => 'info',
                'url'     => route('loans.show', $application->id)
            ]));

            DB::commit();

            $popupData = [
                'title'   => 'Analisis Selesai',
                'score'   => $score,
                'message' => $userMsg,
                'status'  => $status
            ];

            return redirect()->route('loans.show', $application->id)
                ->with('success', "Pengajuan berhasil dikirim.")
                ->with('ai_analysis_popup', $popupData);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * HELPER — GENERATE DATA PINJAMAN AKTIF & CICILAN
     * ============================================================
     */
    private function generateActiveLoan(LoanApplication $application)
    {
        $tenorInt = (int) $application->tenor;

        $loan = Loan::create([
            'user_id'           => $application->user_id,
            'application_id'    => $application->id,
            'loan_code'         => 'LN-' . strtoupper(Str::random(8)),
            'total_amount'      => $application->amount,
            'remaining_balance' => $application->amount,
            'status'            => 'active',
            'start_date'        => now(),
            'due_date'          => now()->addMonths($tenorInt),
            'disbursed_at'      => now(),
        ]);

        $monthlyAmount = ceil($application->amount / $tenorInt);

        for ($i = 1; $i <= $tenorInt; $i++) {
            Installment::create([
                'loan_id'            => $loan->id,
                'installment_number' => $i,
                'due_date'           => now()->addMonths($i),
                'amount'             => $monthlyAmount,
                'status'             => 'pending',
            ]);
        }
    }

    /**
     * ============================================================
     * SHOW — DETAIL PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function show(LoanApplication $loan)
    {
        if ($loan->user_id !== Auth::id()) abort(403);

        $loan->load('loan.installments');
        return view('loans.show', compact('loan'));
    }
}
