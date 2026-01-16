<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            // Cek agar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('cash_flows', 'description')) {
                $table->text('description')->nullable()->after('amount');
            }
        });
    }

    public function down()
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            if (Schema::hasColumn('cash_flows', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};