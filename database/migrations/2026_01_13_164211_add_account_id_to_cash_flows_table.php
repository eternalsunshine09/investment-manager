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
        Schema::table('cash_flows', function (Blueprint $table) {
            // Hubungkan ke tabel accounts
            $table->foreignId('account_id')->after('user_id')->constrained('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};