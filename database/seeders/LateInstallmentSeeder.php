<?php

namespace Database\Seeders;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LateInstallmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat User Dummy
        $user = User::create([
            'name' => 'Late Payer',
            'username' => 'late_payer_' . Str::random(5),
            'email' => 'late_' . Str::random(5) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'borrower',
            'phone' => '628999999' . rand(100, 999),
        ]);

        // 2. Buat Loan Application
        $application = LoanApplication::create([
            'user_id' => $user->id,
            'loan_product_id' => LoanProduct::first()->id,
            'amount' => 5000000,
            'tenor' => 3,
            'purpose' => 'Testing Late Payment',
            'status' => 'approved',
            'reviewed_at' => now(),
            'ai_score' => 90,
        ]);

        // 3. Buat Loan Aktif
        $loan = Loan::create([
            'user_id' => $user->id,
            'application_id' => $application->id,
            'loan_code' => 'LN-LATE-' . Str::random(5),
            'total_amount' => 5000000,
            'remaining_balance' => 5000000,
            'status' => 'active',
            'start_date' => now()->subMonths(1),
            'due_date' => now()->addMonths(2),
            'disbursed_at' => now()->subMonths(1),
        ]);

        // 4. Buat Installments dengan berbagai kondisi keterlambatan

        // Kasus 1: Telat 1 Hari (Grace Period)
        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->subDays(1),
            'amount' => 1666666,
            'status' => 'pending', // Masih pending, akan diproses jadi late
        ]);

        // Kasus 2: Telat 3 Hari (Akhir Grace Period)
        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 2,
            'due_date' => now()->subDays(3),
            'amount' => 1666666,
            'status' => 'pending',
        ]);

        // Kasus 3: Telat 4 Hari (Kena Ta'zir)
        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 3,
            'due_date' => now()->subDays(4),
            'amount' => 1666666,
            'status' => 'pending',
            'tazir_amount' => 0, // Harusnya jadi 25000 setelah command jalan
        ]);

        $this->command->info('Created test data for user: ' . $user->email);
    }
}
