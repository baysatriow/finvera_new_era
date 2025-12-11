<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi mass assignment.
     */
    protected $fillable = [
        'user_id',
        'application_id',
        'loan_code',
        'total_amount',
        'remaining_balance',
        'status',          // active, paid, default, past_due
        'start_date',
        'due_date',
        'disbursed_at',
    ];

    /**
     * Casting otomatis untuk format angka & tanggal.
     */
    protected $casts = [
        'total_amount'       => 'decimal:2',
        'remaining_balance'  => 'decimal:2',
        'start_date'         => 'date',
        'due_date'           => 'date',
        'disbursed_at'       => 'datetime',
    ];

    /**
     * Relasi: Loan dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Loan ini berasal dari satu pengajuan.
     */
    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'application_id');
    }

    /**
     * Relasi: Loan memiliki banyak jadwal cicilan.
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
