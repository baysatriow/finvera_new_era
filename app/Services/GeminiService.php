<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;

    /**
     * ============================================================
     * CONSTRUCTOR — SET API CONFIGURATION
     * ============================================================
     */
    public function __construct()
    {
        $this->apiKey  = env('GEMINI_API_KEY');

        // Menggunakan model Flash (cepat & hemat biaya)
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * ============================================================
     * KYC ANALYSIS — OCR KTP & FACE MATCHING
     * ============================================================
     */
    public function verifyIdentity($ktpPath, $selfiePath)
    {
        $prompt = "
            Bertindaklah sebagai verifikator identitas (KYC) AI profesional.
            Saya mengirimkan dua gambar:
            1. Gambar KTP (Kartu Tanda Penduduk Indonesia).
            2. Gambar Selfie pemegang KTP.

            Tugas Anda:
            1. Ekstrak NIK (Nomor Induk Kependudukan) 16 digit dan Nama Lengkap dari KTP.
            2. Lakukan Face Matching: Bandingkan wajah di foto KTP dengan wajah di foto Selfie.
            3. Deteksi keaslian: Apakah dokumen terlihat seperti hasil editan/palsu?

            Kembalikan HANYA format JSON valid (tanpa markdown):
            {
                'nik': 'string',
                'name': 'string',
                'face_match_score': integer (0-100),
                'is_valid': boolean,
                'reason': 'string'
            }
        ";

        return $this->sendRequestWithImages($prompt, [
            $ktpPath,
            $selfiePath,
        ]);
    }

    /**
     * ============================================================
     * CREDIT SCORING — RISK ASSESSMENT PINJAMAN
     * ============================================================
     */
    public function analyzeCreditProfile(
        $user,
        $loanAmount = 0,
        $tenor = 0,
        $purpose = '',
        $assetType = ''
    ) {
        /**
         * ------------------------------------------------------------
         * PROFIL PEMINJAM
         * ------------------------------------------------------------
         */
        $profileData = "
            Profil Peminjam:
            - Pekerjaan: {$user->job}
            - Gaji Bulanan: Rp " . number_format($user->monthly_income, 0, ',', '.') . "
            - Lama Kerja: {$user->employment_duration} bulan
            - Status KYC: {$user->kyc_status}

            Detail Pengajuan:
            - Nominal Pinjaman: Rp " . number_format($loanAmount, 0, ',', '.') . "
            - Tenor: {$tenor} bulan
            - Tujuan: {$purpose}
            - Aset Jaminan: {$assetType}
        ";

        $prompt = "
            Bertindaklah sebagai Senior Credit Analyst Syariah AI.
            Nilai kelayakan pinjaman Qardh (tanpa bunga) berdasarkan data berikut:
            {$profileData}

            Kriteria Penilaian:
            1. Capacity: Kemampuan bayar (asumsi biaya hidup 50%).
            2. Capital: Nilai & likuiditas aset jaminan.
            3. Character: Stabilitas pekerjaan.
            4. Purpose: Tujuan produktif / mendesak vs konsumtif.

            Kembalikan HANYA format JSON valid:
            {
                'credit_score': integer (0-100),
                'recommendation': 'APPROVE' | 'REJECT' | 'REVIEW',
                'user_message': 'Penjelasan transparan untuk user',
                'admin_analysis': 'Analisis risiko teknis untuk admin',
                'financial_advice': 'Saran keuangan singkat'
            }
        ";

        return $this->sendTextRequest($prompt);
    }

    /**
     * ============================================================
     * INTERNAL HELPER — TEXT REQUEST
     * ============================================================
     */
    protected function sendTextRequest($prompt)
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ];

        return $this->executeApi($payload);
    }

    /**
     * ============================================================
     * INTERNAL HELPER — REQUEST WITH IMAGES
     * ============================================================
     */
    protected function sendRequestWithImages($prompt, array $imagePaths)
    {
        $parts = [
            ['text' => $prompt],
        ];

        foreach ($imagePaths as $path) {
            $fullPath = storage_path('app/public/' . $path);

            if (!file_exists($fullPath)) {
                continue;
            }

            $parts[] = [
                'inline_data' => [
                    'mime_type' => mime_content_type($fullPath),
                    'data'      => base64_encode(file_get_contents($fullPath)),
                ],
            ];
        }

        $payload = [
            'contents' => [
                [
                    'parts' => $parts,
                ],
            ],
        ];

        return $this->executeApi($payload);
    }

    /**
     * ============================================================
     * CORE — EXECUTE GEMINI API REQUEST
     * ============================================================
     */
    protected function executeApi(array $payload)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            /**
             * --------------------------------------------------------
             * HANDLE API FAILURE
             * --------------------------------------------------------
             */
            if ($response->failed()) {
                $errorMsg = 'API Error (' . $response->status() . '): ' . $response->body();
                Log::error('Gemini API Error: ' . $errorMsg);

                return ['error' => $errorMsg];
            }

            $data = $response->json();

            /**
             * --------------------------------------------------------
             * VALIDASI STRUKTUR RESPONSE
             * --------------------------------------------------------
             */
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'error' => 'Invalid Gemini response structure: ' . json_encode($data),
                ];
            }

            /**
             * --------------------------------------------------------
             * PARSE JSON RESPONSE
             * --------------------------------------------------------
             */
            $rawText   = $data['candidates'][0]['content']['parts'][0]['text'];
            $cleanJson = str_replace(['```json', '```'], '', $rawText);
            $parsed    = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'error' => 'JSON Parse Error: ' . json_last_error_msg(),
                    'raw'   => $rawText,
                ];
            }

            return $parsed;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            Log::error('Gemini Connection Error: ' . $e->getMessage());

            return [
                'error' => 'Connection Timeout: Gagal terhubung ke Google AI.',
            ];

        } catch (\Exception $e) {

            Log::error('Gemini Service Exception: ' . $e->getMessage());

            return [
                'error' => 'System Error: ' . $e->getMessage(),
            ];
        }
    }
}
