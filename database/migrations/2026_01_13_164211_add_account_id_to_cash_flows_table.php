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
        // Cek dulu apakah kolom SUDAH ada
        if (!Schema::hasColumn('cash_flows', 'account_id')) {
            Schema::table('cash_flows', function (Blueprint $table) {
                // Jika belum ada, baru buat kolomnya
                $table->foreignId('account_id')->constrained()->onDelete('cascade')->after('user_id');
            });
        }
    }

    public function down()
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};