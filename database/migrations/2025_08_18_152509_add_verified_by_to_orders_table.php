<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by')->nullable()->after('id');

            // Kalau kamu ada tabel users/admins, bisa tambahin foreign key
            $table->foreign('verified_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn('verified_by');
        });
    }
};

