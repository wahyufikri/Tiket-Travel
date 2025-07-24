<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auto_schedules', function (Blueprint $table) {
    $table->unsignedTinyInteger('weekday')->default(1); // default Senin
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_schedules', function (Blueprint $table) {
            //
        });
    }
};
