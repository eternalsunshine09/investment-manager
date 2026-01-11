<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('watchlists', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('code'); // Kode: BBCA, BTC
        $table->string('name')->nullable(); // Nama: Bank Central Asia
        $table->decimal('target_price', 15, 2); // Harga Incaran
        $table->decimal('current_price', 15, 2)->nullable(); // Harga Pasar (Manual update dulu)
        $table->text('note')->nullable(); // Alasan beli: "Tunggu support kuat"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};