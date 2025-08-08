<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasinya.
     */
    public function up(): void
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->integer('travel_minutes')
                  ->default(0)
                  ->after('stop_order')
                  ->comment('Waktu tempuh dari titik awal rute ke stop ini, dalam menit');
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('route_stops', function (Blueprint $table) {
            $table->dropColumn('travel_minutes');
        });
    }
};
