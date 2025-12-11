<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /* ============================================================
     * DASHBOARD BORROWER / ADMIN REDIRECT
     * ============================================================ */
    public function index()
    {
        $user = Auth::user();

        // Redirect khusus admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // User borrower wajib melengkapi alamat
        if (empty($user->address_full)) {
            return redirect()->route('register.step2');
        }

        // NOTE:
        // Tidak ada pemanggilan AI langsung di halaman ini (agar loading instan).
        return view('dashboard.borrower');
    }

    /* ============================================================
     * API AJAX — Ambil Saran AI secara Asinkron
     * ============================================================ */
    public function getAiAdvice()
    {
        $user = Auth::user();

        // Cache 24 jam — mempercepat respons setelah pemanggilan pertama
        $advice = Cache::remember(
            'ai_advice_' . $user->id,
            60 * 24,
            function () use ($user) {
                $analysis = $this->geminiService->analyzeCreditProfile($user);
                return $analysis['financial_advice']
                    ?? 'Kelola keuangan Anda dengan bijak dan hindari utang konsumtif berlebih.';
            }
        );

        return response()->json([
            'advice' => $advice,
        ]);
    }
}
