<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stop_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('from_stop_id')->constrained('route_stops')->onDelete('cascade');
            $table->foreignId('to_stop_id')->constrained('route_stops')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Optional: pastikan kombinasi unik agar tidak duplikat harga untuk pasangan stop yang sama
            $table->unique(['route_id', 'from_stop_id', 'to_stop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stop_prices');
    }
};
