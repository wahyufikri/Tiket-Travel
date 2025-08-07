<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']); // jenis transaksi
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // kalau income dari order online
            $table->string('title'); // nama transaksi
            $table->decimal('amount', 15, 2); // nominal
            $table->string('category')->nullable(); // kategori: operasional, gaji, tiket, dll
            $table->date('transaction_date'); // tanggal transaksi
            $table->string('payment_method', 50)->nullable(); // cash, transfer, dll
            $table->string('description')->nullable(); // keterangan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
