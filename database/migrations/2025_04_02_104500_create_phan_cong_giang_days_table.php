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
        Schema::create('phan_cong_giang_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->foreignId('tro_giang_id')->constrained('tro_giangs')->onDelete('cascade');
            $table->timestamp('ngay_bat_dau')->nullable();
            $table->timestamp('ngay_ket_thuc')->nullable();
            $table->string('trang_thai')->default('dang_hoat_dong')->comment('dang_hoat_dong, tam_ngung, ket_thuc');
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
        Schema::dropIfExists('phan_cong_giang_days');
    }
}; 