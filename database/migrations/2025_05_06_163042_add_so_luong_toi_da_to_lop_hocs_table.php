<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->integer('so_luong_toi_da')->default(30)->after('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->dropColumn('so_luong_toi_da');
        });
    }
};
