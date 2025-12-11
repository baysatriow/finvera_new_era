<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass assignment.
     */
    protected $fillable = [
        'user_id',
        'loan_product_id',
        'amount',
        'tenor',
        'purpose',
        'asset_document_path',
        'asset_selfie_path',
        'asset_value',
        'status',              // pending, approved, rejected, canceled
        'ai_score',
        'admin_note',
        'reviewed_at',
    ];

    /**
     * Cast otomatis untuk angka & tanggal.
     */
    protected $casts = [
        'amount'        => 'decimal:2',
        'asset_value'   => 'decimal:2',
        'reviewed_at'   => 'datetime',
    ];

    /**
     * Relasi: Pengajuan dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Pengajuan menggunakan satu produk pinjaman.
     */
    public function product()
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    /**
     * Relasi: Jika disetujui, pengajuan memiliki satu Loan aktif.
     */
    public function loan()
    {
        return $this->hasOne(Loan::class, 'application_id');
    }
}
