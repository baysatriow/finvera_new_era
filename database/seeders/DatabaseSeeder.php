<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LoanProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator FinVera',
            'username' => 'admin',
            'email' => 'admin@finvera.com',
            'password' => Hash::make('password123'), // Default password
            'role' => 'admin',
            'phone' => '6281234567890',
            'email_verified_at' => now(),
        ]);

        // 2. Buat Akun Peminjam (Contoh User)
        User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_borrower',
            'email' => 'user@finvera.com',
            'password' => Hash::make('password123'),
            'role' => 'borrower',
            'phone' => '6289876543210',
            'monthly_income' => 5000000,
            'job' => 'Karyawan Swasta',
            'kyc_status' => 'unverified', // Belum verifikasi
            'email_verified_at' => now(),
        ]);

        // 3. Buat Produk Pinjaman (Qardh 20 Juta)
        LoanProduct::create([
            'name' => 'Qardh - Dana Cepat Syariah',
            'min_amount' => 1000000,   // Min 1 Juta
            'max_amount' => 20000000,  // Max 20 Juta (Sesuai Request)
            'tenor_options' => [1, 3, 6, 12], // Pilihan bulan
            'admin_fee' => 50000,      // Biaya admin flat (contoh)
        ]);
    }
}
