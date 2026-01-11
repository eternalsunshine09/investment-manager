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
        Schema::table('products', function (Blueprint $table) {
            // Kolom ini boleh kosong (nullable) karena Saham/Crypto gak butuh sub-kategori
            $table->string('sub_category')->nullable()->after('category'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('sub_category');
    });
}
};