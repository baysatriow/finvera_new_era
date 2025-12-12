<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $geminiService;

    /* ============================================================
     * CONSTRUCTOR
     * ============================================================ */
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /* ============================================================
     * INDEX — HALAMAN DASHBOARD USER
     * ============================================================ */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (empty($user->address_full)) {
            return redirect()->route('register.step2');
        }

        return view('dashboard.borrower');
    }

    /* ============================================================
     * API — AMBIL SARAN AI (NON BLOCKING / ASYNC)
     * ============================================================ */
    public function getAiAdvice()
    {
        $user     = Auth::user();
        $cacheKey = 'ai_advice_' . $user->id;

        // 1. Cek cache (5 menit)
        if (Cache::has($cacheKey)) {
            return response()->json([
                'advice' => Cache::get($cacheKey),
            ]);
        }

        // 2. Tutup session agar request tidak blocking
        Session::save();
        session_write_close();

        try {
            // 3. Proses analisis AI (berat)
            $analysis = $this->geminiService->analyzeCreditProfile($user);
            $advice   = $analysis['financial_advice']
                ?? 'Tetap jaga kesehatan finansial Anda dengan bijak.';

            // 4. Simpan cache (300 detik)
            Cache::put($cacheKey, $advice, 300);

            return response()->json(['advice' => $advice]);

        } catch (\Exception $e) {
            return response()->json([
                'advice' => 'Layanan AI sedang sibuk, silakan coba beberapa saat lagi.'
            ], 500);
        }
    }
}
