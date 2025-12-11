<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pengajuan Pinjaman
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('loan_product_id')->constrained('loan_products');
            $table->decimal('amount', 15, 2);
            $table->integer('tenor'); // Dalam bulan
            $table->text('purpose'); // Tujuan pinjaman
            $table->string('asset_document_path')->nullable(); // Dokumen aset
            $table->string('asset_selfie_path')->nullable(); // Selfie dengan aset
            $table->decimal('asset_value', 15, 2)->default(0);

            // Status Approval [cite: 83]
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->decimal('ai_score', 5, 2)->nullable(); // Skor kelayakan AI
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Tabel Pinjaman Aktif
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('application_id')->constrained('loan_applications');
            $table->string('loan_code')->unique(); // Kode unik pinjaman
            $table->decimal('total_amount', 15, 2); // Pokok + Admin Fee (jika digabung)
            $table->decimal('remaining_balance', 15, 2);

            // Status Pinjaman
            $table->enum('status', ['active', 'paid', 'default', 'past_due'])->default('active');
            $table->date('start_date');
            $table->date('due_date'); // Tanggal jatuh tempo akhir
            $table->timestamp('disbursed_at')->nullable(); // Waktu pencairan dana
            $table->timestamps();
        });

        // Tabel Cicilan & Denda Sosial (Ta'zir)
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->integer('installment_number'); // Cicilan ke-1, ke-2, dst
            $table->date('due_date');
            $table->decimal('amount', 15, 2); // Jumlah tagihan pokok
            $table->decimal('tazir_amount', 15, 2)->default(0); // Denda sosial [cite: 71]
            $table->decimal('tawidh_amount', 15, 2)->default(0); // Ganti rugi operasional [cite: 72]
            $table->decimal('total_paid', 15, 2)->default(0);

            $table->enum('status', ['pending', 'paid', 'late'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_applications');
    }
};
