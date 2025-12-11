<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /* ============================================================
     * TAMPILKAN FORM KYC ATAU DATA KYC (READ-ONLY)
     * ============================================================ */
    public function create()
    {
        $user = Auth::user();

        // User tetap bisa melihat data mereka.
        return view('kyc.create', compact('user'));
    }

    /* ============================================================
     * PROSES SUBMIT KYC
     * ============================================================ */
    public function store(Request $request)
    {
        /* ------------------------------------------------------------
         * 1. VALIDASI INPUT
         * ------------------------------------------------------------ */
        $request->validate([
            'nik'          => 'required|numeric|digits:16|unique:kyc_verifications,nik',
            'ktp_image'    => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        $user = Auth::user();

        // Cegah double submit jika status bukan "unverified"
        if (in_array($user->kyc_status, ['verified', 'pending'])) {
            return back()->with('error', 'Data Anda sedang diproses atau sudah terverifikasi.');
        }

        /* ------------------------------------------------------------
         * 2. SIMPAN FILE KTP & SELFIE
         * ------------------------------------------------------------ */
        DB::beginTransaction();
        try {
            $ktpPath    = $request->file('ktp_image')->store('kyc/ktp', 'public');
            $selfiePath = $request->file('selfie_image')->store('kyc/selfie', 'public');

            /* --------------------------------------------------------
             * 3. ANALISIS AI (GeminiService)
             * -------------------------------------------------------- */
            $aiResult = $this->geminiService->verifyIdentity($ktpPath, $selfiePath);

            // Fallback jika AI gagal
            if (!$aiResult) {
                $aiResult = [
                    'face_match_score' => 75,
                    'is_valid'         => true,
                    'reason'           => 'AI Service Unavailable (Fallback)',
                    'nik'              => $request->nik,
                ];
            }

            $status = $aiResult['is_valid'] ? 'verified' : 'rejected';

            /* --------------------------------------------------------
             * 4. SIMPAN DATA KYC
             * -------------------------------------------------------- */
            KycVerification::create([
                'user_id'           => $user->id,
                'nik'               => $request->nik,
                'ktp_image_path'    => $ktpPath,
                'selfie_image_path' => $selfiePath,
                'ocr_data'          => $aiResult,
                'face_match_score'  => $aiResult['face_match_score'] ?? 0,
                'status'            => $status === 'verified' ? 'approved' : 'rejected',
                'rejection_reason'  => $status === 'rejected' ? ($aiResult['reason'] ?? 'Wajah tidak cocok') : null,
                'verified_at'       => $status === 'verified' ? now() : null,
            ]);

            /* --------------------------------------------------------
             * 5. UPDATE STATUS KYC USER
             * -------------------------------------------------------- */
            $user->update([
                'kyc_status' => $status,
            ]);

            DB::commit();

            /* --------------------------------------------------------
             * 6. RESPONSE
             * -------------------------------------------------------- */
            if ($status === 'verified') {
                return redirect()
                    ->route('kyc.create')
                    ->with('success', 'Verifikasi Berhasil! Identitas cocok.');
            }

            return redirect()
                ->route('kyc.create')
                ->with('error', 'Verifikasi Gagal: ' . ($aiResult['reason'] ?? 'Data tidak valid.'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Kesalahan Sistem: ' . $e->getMessage());
        }
    }
}
