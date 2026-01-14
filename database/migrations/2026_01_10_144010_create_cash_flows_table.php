<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Kolom-kolom sesuai CSV
            $table->enum('type', ['income', 'expense']); // Income / Expenses
            $table->string('category');                  // Food & Drinks, dll
            $table->string('currency', 3)->default('IDR'); // IDR, USD
            $table->decimal('amount', 20, 2);
            $table->date('date');
            $table->text('note')->nullable();            // Catatan transaksi
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};