<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi mass assignment.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',                 // admin, borrower
        'admin_level',          // master, staff
        'phone',
        'date_of_birth',
        'job',
        'monthly_income',
        'employment_duration',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'address_full',
        'kyc_status',           // unverified, pending, verified, rejected
        'credit_score',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting untuk otomatis format data.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'monthly_income'    => 'decimal:2',
            'date_of_birth'     => 'date',
        ];
    }

    /**
     * Relasi: User punya satu data KYC.
     */
    public function kyc()
    {
        return $this->hasOne(KycVerification::class);
    }

    /**
     * Relasi: User punya banyak aplikasi pinjaman.
     */
    public function applications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    /**
     * Relasi: User punya banyak pinjaman aktif.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relasi: User punya banyak rekening bank.
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
