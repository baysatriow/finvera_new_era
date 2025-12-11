<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Produk Pinjaman
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Qardh
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2); // Limit 20 Juta sesuai request
            $table->json('tenor_options'); // Contoh: [1, 3, 6, 12] bulan
            $table->decimal('admin_fee', 15, 2)->default(0); // Biaya admin flat jika ada
            $table->timestamps();
        });

        // Tabel Verifikasi KYC
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('ktp_image_path');
            $table->string('selfie_image_path');
            $table->json('ocr_data')->nullable(); // Hasil scan AI
            $table->decimal('face_match_score', 5, 2)->nullable(); // Skor AI
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
        Schema::dropIfExists('loan_products');
    }
};
