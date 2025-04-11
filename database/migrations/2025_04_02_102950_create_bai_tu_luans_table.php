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
        Schema::create('bai_tu_luans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('bai_tap_id')->constrained('bai_taps')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->text('noi_dung');
            $table->string('trang_thai')->default('cho_cham')->comment('cho_cham, da_cham');
            $table->float('diem')->nullable();
            $table->float('diem_toi_da')->default(10);
            $table->text('nhan_xet')->nullable();
            $table->foreignId('nguoi_cham_id')->nullable()->constrained('giao_viens');
            $table->timestamp('ngay_nop')->nullable();
            $table->timestamp('ngay_cham')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_tu_luans');
    }
};
