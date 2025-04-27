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
        Schema::create('thanh_toans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('dang_ky_hoc_id')->constrained('dang_ky_hocs')->onDelete('cascade');
            $table->string('ma_thanh_toan')->unique();
            $table->string('ma_giao_dich')->nullable();
            $table->decimal('so_tien', 12, 2);
            $table->string('noi_dung')->nullable();
            $table->string('phuong_thuc')->default('vnpay');
            $table->string('trang_thai')->default('chua_thanh_toan');
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->string('mo_ta')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toans');
    }
};
