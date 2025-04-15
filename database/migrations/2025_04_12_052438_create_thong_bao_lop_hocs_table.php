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
        Schema::create('thong_bao_lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->foreignId('nguoi_tao')->nullable();
            $table->foreignId('nguoi_sua')->nullable();
            $table->dateTime('ngay_hieu_luc')->nullable()->comment('Ngày bắt đầu hiệu lực thông báo');
            $table->dateTime('ngay_het_han')->nullable()->comment('Ngày hết hạn thông báo, null nếu không có hạn');
            $table->tinyInteger('trang_thai')->default(1)->comment('1: Kích hoạt, 0: Không kích hoạt');
            $table->string('dinh_kem')->nullable()->comment('Đường dẫn tới file đính kèm');
            $table->boolean('hien_thi')->default(true)->comment('Ẩn/hiện thông báo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao_lop_hocs');
    }
};
