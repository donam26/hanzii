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
        Schema::create('luong_tro_giangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tro_giang_id')->constrained('tro_giangs')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->float('tong_hoc_phi_thu_duoc')->default(0)->comment('Tổng học phí thu được từ lớp');
            $table->foreignId('vai_tro_id')->constrained('vai_tros')->comment('Dùng vai trò để xác định tỷ lệ lương');
            $table->float('tong_luong')->default(0)->comment('Lương thực nhận');
            $table->date('ngay_thanh_toan')->nullable();
            $table->string('trang_thai')->default('cho_thanh_toan')->comment('cho_thanh_toan, da_thanh_toan');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luong_tro_giangs');
    }
}; 