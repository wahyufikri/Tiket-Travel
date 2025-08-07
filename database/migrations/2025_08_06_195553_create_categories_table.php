<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kategori transaksi
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // contoh: Operasional, Gaji, Tiket, dll
            $table->enum('type', ['income', 'expense']); // tipe kategori: pemasukan atau pengeluaran
            $table->timestamps();
        });

        // Tabel metode pembayaran

        // Update tabel transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('transaction_categories')
                ->onDelete('set null');


            // Hapus kolom lama jika mau full relasi
            $table->dropColumn('category');

        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('category')->nullable();

            $table->dropForeign(['category_id']);

            $table->dropColumn(['category_id']);
        });

        Schema::dropIfExists('transaction_categories');
    }
};
