<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identitas & Role [cite: 12]
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('role', ['admin', 'borrower'])->default('borrower')->after('email');
            $table->string('phone', 20)->unique()->nullable()->after('role'); // Format 628xx [cite: 23]

            // Data Pribadi & Pekerjaan [cite: 24-29]
            $table->date('date_of_birth')->nullable();
            $table->string('job')->nullable();
            $table->decimal('monthly_income', 15, 2)->default(0);
            $table->integer('employment_duration')->default(0); // Dalam bulan

            // Alamat [cite: 30]
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address_full')->nullable();

            // Status & Scoring [cite: 38, 82]
            $table->enum('kyc_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified');
            $table->integer('credit_score')->default(0); // 0-100
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'role', 'phone',
                'date_of_birth', 'job', 'monthly_income', 'employment_duration',
                'province', 'city', 'district', 'village', 'postal_code', 'address_full',
                'kyc_status', 'credit_score'
            ]);
        });
    }
};
