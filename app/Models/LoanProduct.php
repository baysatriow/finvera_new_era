<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (mass assignment).
     */
    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'tenor_options',   // Disimpan sebagai JSON di database
        'admin_fee',
    ];

    /**
     * Cast otomatis agar data numerik & JSON ditangani dengan benar.
     */
    protected $casts = [
        'tenor_options' => 'array',      // JSON → Array
        'min_amount'    => 'decimal:2',  // Pembulatan 2 decimal
        'max_amount'    => 'decimal:2',
        'admin_fee'     => 'decimal:2',
    ];
}
