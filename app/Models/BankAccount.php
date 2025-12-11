<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi mass-assignment.
     */
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'is_primary',
    ];

    /**
     * Relasi: Akun bank dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
