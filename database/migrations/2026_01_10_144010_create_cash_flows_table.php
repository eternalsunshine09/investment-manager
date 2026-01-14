<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_xx_xx_create_cash_flows_table.php

public function up(): void
{
    Schema::create('cash_flows', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Kita buat nullable dulu, nanti diisi saat import jika nama akun cocok
        $table->foreignId('account_id')->nullable()->constrained()->onDelete('cascade'); 
        
        // Sesuaikan dengan data CSV (Income/Expenses)
        $table->enum('type', ['income', 'expense']); 
        
        $table->string('category'); // Tambahan kolom kategori
        $table->decimal('amount', 20, 2);
        $table->date('date');
        $table->string('description')->nullable(); // Untuk kolom 'note' dari CSV
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};