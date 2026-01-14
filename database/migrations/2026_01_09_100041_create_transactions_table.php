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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            
            $table->date('transaction_date');
            $table->enum('type', ['beli', 'jual', 'dividen_cash', 'dividen_unit', 'stock_split', 'reverse_split', 'right_issue']); 
            
            $table->decimal('amount', 20, 8)->default(0);
            $table->decimal('price_per_unit', 20, 2)->default(0);
            
            $table->decimal('fee', 20, 2)->default(0);
            
            $table->decimal('total_value', 20, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};