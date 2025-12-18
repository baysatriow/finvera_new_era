<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            // Menambah kolom alasan penolakan
            $table->text('rejection_reason')->nullable()->after('proof_path');

            // Catatan: Pastikan kolom 'status' di database Anda sudah mendukung enum 'failed'
            // atau ubah menjadi varchar jika perlu.
            // ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid', 'late', 'waiting', 'failed');
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
