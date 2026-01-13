<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_add_currency_to_accounts_table.php
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Default IDR, tapi nanti kita bisa set akun lain jadi USD
            $table->string('currency', 3)->default('IDR')->after('name'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            //
        });
    }
};