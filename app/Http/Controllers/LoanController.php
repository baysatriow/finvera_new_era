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

    /**
     * ============================================================
     * LIST PENGAJUAN PINJAMAN USER
     * ============================================================
     */
    public function index()
    {
        $applications = LoanApplication::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('loans.index', compact('applications'));
    }

    /**
     * ============================================================
     * FORM PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function create()
    {
        $user = Auth::user();

        // Wajib KYC verified
        if ($user->kyc_status !== 'verified') {
            return redirect()
                ->route('kyc.create')
                ->with('warning', 'Silakan verifikasi identitas (KYC) terlebih dahulu.');
        }

        // Wajib punya rekening bank
        if (!BankAccount::where('user_id', $user->id)->exists()) {
            return redirect()
                ->route('bank.create')
                ->with('warning', 'Anda wajib mendaftarkan rekening bank pencairan sebelum mengajukan pinjaman.');
        }

        // Cek apakah ada pengajuan pending
        $hasPendingApp = LoanApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApp) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Anda masih memiliki pengajuan yang sedang diproses. Mohon tunggu.');
        }

        // Cek pinjaman aktif
        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->whereIn('status', ['active', 'past_due', 'default'])
            ->exists();

        if ($hasActiveLoan) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda masih memiliki pinjaman aktif yang belum lunas. Silakan lunasi terlebih dahulu.');
        }

        $product = LoanProduct::first();
        return view('loans.create', compact('product'));
    }

    /**
     * ============================================================
     * SUBMIT PENGAJUAN PINJAMAN
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

        // Upload dokumen
        $assetDocPath    = $request->file('asset_document')->store('assets', 'public');
        $assetSelfiePath = $request->file('asset_selfie')->store('assets', 'public');

        // Analisis AI
        $aiAnalysis = $this->geminiService->analyzeCreditProfile(
            $user,
            $request->amount,
            $request->tenor,
            $request->purpose,
            $request->asset_type
        );

        $status = 'pending';
        if (!$aiAnalysis) {
            $score     = 75;
            $userMsg   = "Analisis AI tertunda, menunggu review manual.";
            $adminNote = "AI Service Timeout.";
        } else {
            $score     = $aiAnalysis['credit_score'];
            $userMsg   = $aiAnalysis['user_message'];
            $adminNote = "Risk Analysis: " . $aiAnalysis['admin_analysis'];

            if ($score >= 75) {
                $adminNote .= " [AI RECOMMENDATION: HIGHLY RECOMMENDED TO APPROVE]";
            }
        }

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
                'reviewed_at'        => null,
            ]);

            // Update credit score user
            $user->update(['credit_score' => $score]);

            DB::commit();

            // Data Swal popup
            $popupData = [
                'title'   => 'Analisis Selesai',
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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * GENERATE LOAN AKTIF SETELAH DISETUJUI ADMIN
     * ============================================================
     */
    private function generateActiveLoan(LoanApplication $application)
    {
        $tenor = (int) $application->tenor;

        $loan = Loan::create([
            'user_id'           => $application->user_id,
            'application_id'    => $application->id,
            'loan_code'         => 'LN-' . strtoupper(Str::random(8)),
            'total_amount'      => $application->amount,
            'remaining_balance' => $application->amount,
            'status'            => 'active',
            'start_date'        => now(),
            'due_date'          => now()->addMonths($tenor),
            'disbursed_at'      => now(),
        ]);

        $monthlyAmount = ceil($application->amount / $tenor);

        for ($i = 1; $i <= $tenor; $i++) {
            Installment::create([
                'loan_id'           => $loan->id,
                'installment_number'=> $i,
                'due_date'          => now()->addMonths($i),
                'amount'            => $monthlyAmount,
                'status'            => 'pending',
            ]);
        }
    }

    /**
     * ============================================================
     * DETAIL PENGAJUAN PINJAMAN
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
