<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

return new class extends Migration
{
    public function up()
    {
        // Cara paling aman untuk mengubah ENUM ke VARCHAR di MySQL
        // Kita ubah jadi VARCHAR(50) agar muat 'topup', 'withdrawal', 'buy', dll.
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type VARCHAR(50) NOT NULL");
    }

    public function down()
    {
        // Kembalikan ke ENUM jika rollback (sesuaikan dengan enum lama anda jika perlu)
        // DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('buy', 'sell') NOT NULL");
    }
};