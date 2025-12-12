<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Notifications\SystemNotification;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function create()
    {
        $user = Auth::user();
        return view('kyc.create', compact('user'));
    }

    /**
     * ============================================================
     * STORE — PROSES SUBMIT KYC
     * ============================================================
     */
    public function store(Request $request)
    {
        // ------------------------------------------------------------
        // 1. Validasi Input
        // ------------------------------------------------------------
        $request->validate([
            'nik'          => 'required|numeric|digits:16|unique:kyc_verifications,nik',
            'ktp_image'    => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus 16 digit.',
            'nik.unique'   => 'NIK ini sudah terdaftar dalam sistem.',
            'ktp_image.max' => 'Ukuran foto KTP maksimal 5MB.',
            'selfie_image.max' => 'Ukuran foto Selfie maksimal 5MB.',
        ]);

        $user = Auth::user();

        // ------------------------------------------------------------
        // Cegah user submit ulang jika KYC sudah pending/verified
        // ------------------------------------------------------------
        if (in_array($user->kyc_status, ['verified', 'pending'])) {
            return back()->with('info', 'Verifikasi Anda sedang diproses atau sudah disetujui.');
        }

        DB::beginTransaction();

        try {
            // ------------------------------------------------------------
            // 2. Upload File KTP & Selfie
            // ------------------------------------------------------------
            $ktpPath    = Storage::disk('public')->put('kyc/ktp', $request->file('ktp_image'));
            $selfiePath = Storage::disk('public')->put('kyc/selfie', $request->file('selfie_image'));

            // ------------------------------------------------------------
            // 3. Analisis AI (Gemini Service)
            // ------------------------------------------------------------
            $aiResult = $this->geminiService->verifyIdentity($ktpPath, $selfiePath);

            $status  = 'rejected';
            $score   = 0;
            $reason  = 'Gagal terhubung ke layanan verifikasi.';

            if ($aiResult) {
                $score   = $aiResult['face_match_score'] ?? 0;
                $isValid = $aiResult['is_valid'] ?? false;
                $reason  = $aiResult['reason'] ?? 'Data wajah tidak cocok atau dokumen buram.';

                if ($isValid && $score >= 75) {
                    $status = 'verified';
                }
            } else {
                $reason = "Layanan AI tidak merespons. Silakan coba lagi.";
            }

            // ------------------------------------------------------------
            // 4. Simpan Data KYC ke Database
            // ------------------------------------------------------------
            KycVerification::create([
                'user_id'           => $user->id,
                'nik'               => $request->nik,
                'ktp_image_path'    => $ktpPath,
                'selfie_image_path' => $selfiePath,
                'ocr_data'          => $aiResult,
                'face_match_score'  => $score,
                'status'            => $status === 'verified' ? 'approved' : 'rejected',
                'rejection_reason'  => $status === 'rejected' ? $reason : null,
                'verified_at'       => $status === 'verified' ? now() : null,
            ]);

            // Update status KYC user
            $user->update([
                'kyc_status' => $status
            ]);

            // ------------------------------------------------------------
            // 5. Kirim Notifikasi ke User
            // ------------------------------------------------------------
            if ($status === 'verified') {
                $user->notify(new SystemNotification([
                    'title'   => 'KYC Berhasil',
                    'message' => 'Selamat! Identitas Anda telah terverifikasi. Anda sekarang dapat mengajukan pinjaman.',
                    'type'    => 'success',
                    'url'     => route('loans.create')
                ]));

            } else {
                $user->notify(new SystemNotification([
                    'title'   => 'KYC Ditolak',
                    'message' => 'Maaf, verifikasi identitas Anda ditolak. Silakan periksa alasan dan unggah ulang dokumen.',
                    'type'    => 'danger',
                    'url'     => route('kyc.create')
                ]));
            }

            DB::commit();

            // ------------------------------------------------------------
            // 6. Redirect Result
            // ------------------------------------------------------------
            if ($status === 'verified') {
                return redirect()
                    ->route('kyc.create')
                    ->with('success', 'Selamat! Identitas Berhasil Diverifikasi.');
            }

            return redirect()
                ->route('kyc.create')
                ->with('error', 'Verifikasi Ditolak: ' . $reason . ' Silakan perbaiki foto dan coba lagi.');

        } catch (\Exception $e) {
            DB::rollback();

            if (isset($ktpPath))    Storage::disk('public')->delete($ktpPath);
            if (isset($selfiePath)) Storage::disk('public')->delete($selfiePath);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
