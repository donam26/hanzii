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
        Schema::create('thong_ke_tai_chinhs', function (Blueprint $table) {
            $table->id();
            $table->integer('thang');
            $table->integer('nam');
            $table->float('tong_thu')->default(0)->comment('Tổng thu từ học phí');
            $table->float('tong_chi')->default(0)->comment('Tổng chi trả lương');
            $table->float('loi_nhuan')->default(0);
            $table->text('ghi_chu')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_ke_tai_chinhs');
    }
};
