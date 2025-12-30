<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi mass-assignment.
     */
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount',
        'tazir_amount',     // Denda sosial
        'tawidh_amount',    // Ganti rugi
        'total_paid',
        'status',
        'proof_path',
        'rejection_reason',
        'paid_at',
        'last_reminder_day',
    ];

    /**
     * Casting otomatis untuk kolom tanggal & numeric.
     */
    protected $casts = [
        'due_date'      => 'date',
        'amount'        => 'decimal:2',
        'tazir_amount'  => 'decimal:2',
        'tawidh_amount' => 'decimal:2',
        'total_paid'    => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    /**
     * Relasi: Cicilan ini milik satu pinjaman.
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
