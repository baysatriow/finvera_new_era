<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey  = env('GEMINI_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Analisis KYC:
     * - OCR KTP
     * - Face Matching
     * - Validasi keaslian dokumen
     */
    public function verifyIdentity($ktpPath, $selfiePath)
    {
        $prompt = "
            Bertindaklah sebagai verifikator identitas (KYC) AI profesional.

            Saya mengirimkan:
            1. Foto KTP.
            2. Foto Selfie pemilik KTP.

            Tugas:
            - Ekstrak NIK & Nama lengkap.
            - Hitung face match score (0–100).
            - Deteksi keaslian dokumen.

            Kembalikan format JSON valid:
            {
                'nik': 'string',
                'name': 'string',
                'face_match_score': integer,
                'is_valid': boolean,
                'reason': 'string'
            }
        ";

        return $this->sendRequestWithImages($prompt, [$ktpPath, $selfiePath]);
    }

    /**
     * Analisis Credit Score & Risiko Pengajuan.
     */
    public function analyzeCreditProfile($user, $loanAmount = 0, $tenor = 0, $purpose = '', $assetType = '')
    {
        $profileData = "
            Profil Peminjam:
            - Pekerjaan: {$user->job}
            - Gaji Bulanan: Rp " . number_format($user->monthly_income, 0, ',', '.') . "
            - Lama Kerja: {$user->employment_duration} bulan
            - Status KYC: {$user->kyc_status}

            Pengajuan:
            - Nominal: Rp " . number_format($loanAmount, 0, ',', '.') . "
            - Tenor: {$tenor} bulan
            - Tujuan: {$purpose}
            - Aset: {$assetType}
        ";

        $prompt = "
            Bertindaklah sebagai Senior Credit Analyst Syariah.
            Nilai kelayakan pinjaman Qardh berdasarkan:
            - Capacity
            - Capital
            - Character
            - Purpose

            $profileData

            Kembalikan JSON valid:
            {
                'credit_score': integer,
                'recommendation': 'APPROVE' | 'REJECT' | 'REVIEW',
                'user_message': 'string',
                'admin_analysis': 'string',
                'financial_advice': 'string'
            }
        ";

        return $this->sendTextRequest($prompt);
    }

    /* ============================================================
       Internal Helpers
       ============================================================ */

    /**
     * Request teks (tanpa gambar).
     */
    protected function sendTextRequest($prompt)
    {
        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];

        return $this->executeApi($payload);
    }

    /**
     * Request dengan melampirkan gambar (Base64).
     */
    protected function sendRequestWithImages($prompt, array $imagePaths)
    {
        $parts = [
            ['text' => $prompt]
        ];

        // Konversi setiap gambar ke inline base64
        foreach ($imagePaths as $path) {
            $file = public_path('storage/' . $path);

            if (file_exists($file)) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => mime_content_type($file),
                        'data'      => base64_encode(file_get_contents($file)),
                    ]
                ];
            }
        }

        $payload = [
            'contents' => [
                ['parts' => $parts]
            ]
        ];

        return $this->executeApi($payload);
    }

    /**
     * Eksekusi request API ke Gemini.
     */
    protected function executeApi(array $payload)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return null;
            }

            $json    = $response->json();
            $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            $clean = str_replace(['```json', '```'], '', $rawText);

            return json_decode($clean, true);

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
