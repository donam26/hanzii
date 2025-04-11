<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrangThaiToBaiTapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bai_taps', function (Blueprint $table) {
            $table->enum('trang_thai', ['chua_xuat_ban', 'da_xuat_ban'])->default('chua_xuat_ban');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bai_taps', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
}
