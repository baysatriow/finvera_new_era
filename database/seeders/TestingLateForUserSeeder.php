<?php

namespace Database\Seeders;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestingLateForUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 3;
        $user = User::find($userId);

        if (!$user) {
            $this->command->error("User with ID $userId not found. Creating temporary user...");
            $user = User::factory()->create(['id' => 3]);
        }

        $this->command->info("Creating late loan for User: {$user->name} (ID: $userId)");

        // 1. Buat Loan Application
        $application = LoanApplication::create([
            'user_id' => $user->id,
            'loan_product_id' => LoanProduct::first()->id ?? LoanProduct::factory()->create()->id,
            'amount' => 10000000,
            'tenor' => 6,
            'purpose' => 'Testing Late Specific User',
            'status' => 'approved',
            'reviewed_at' => now(),
            'ai_score' => 95,
        ]);

        // 2. Buat Loan Aktif
        $loan = Loan::create([
            'user_id' => $user->id,
            'application_id' => $application->id,
            'loan_code' => 'LN-TEST-' . Str::upper(Str::random(6)),
            'total_amount' => 10000000,
            'remaining_balance' => 10000000,
            'status' => 'active',
            'start_date' => now()->subMonths(2),
            'due_date' => now()->addMonths(4),
            'disbursed_at' => now()->subMonths(2),
        ]);

        // 3. Buat Installment TELAT 4 HARI (Target Ta'zir)
        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->subDays(4), // H-4 -> Trigger Denda & Notif
            'amount' => 1666666,
            'status' => 'pending', 
            'tazir_amount' => 0,
        ]);

        $this->command->info("Done! Created Installment H-4 for User ID 3.");
        $this->command->info("Please run 'php artisan app:process-late-installments' to apply penalty.");
    }
}
