<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->string('proof_path')->nullable()->after('status');
            // Kita perluas enum status jika database support alter enum,
            // atau kita asumsikan logika aplikasi menangani string 'waiting'.
            // Untuk aman di Laravel migration tanpa raw SQL ribet, kita anggap kolom status adalah string/varchar atau kita ubah definisinya.
            // Jika status sebelumnya ENUM, kita perlu mengubahnya agar menerima 'waiting'.
            // Disini saya gunakan DB statement untuk mengubah kolom enum (khusus MySQL)

            // Note: Jika error di sqlite (testing), hapus baris DB::statement ini.
            // DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid', 'late', 'waiting') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('proof_path');
        });
    }
};
