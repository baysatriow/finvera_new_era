<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara mass assignment.
     */
    protected $fillable = [
        'user_id',
        'nik',
        'ktp_image_path',
        'selfie_image_path',
        'ocr_data',          // JSON hasil analisis AI
        'face_match_score',
        'status',            // approved, rejected, pending
        'rejection_reason',
        'verified_at',
    ];

    /**
     * Casting otomatis untuk atribut JSON & datetime.
     */
    protected $casts = [
        'ocr_data'    => 'array',
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi: KYC ini milik satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
