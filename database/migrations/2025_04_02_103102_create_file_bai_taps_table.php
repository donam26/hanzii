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
        Schema::create('file_bai_taps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('bai_tap_id')->constrained('bai_taps')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->string('ten_file');
            $table->string('duong_dan_file');
            $table->string('loai_file')->nullable();
            $table->bigInteger('kich_thuoc_file')->nullable();
            $table->timestamp('ngay_nop')->nullable();
            $table->float('diem')->nullable();
            $table->text('nhan_xet')->nullable();
            $table->string('trang_thai')->default('da_nop')->comment('da_nop, da_cham, da_tra_lai');
            $table->foreignId('nguoi_cham_id')->nullable()->constrained('giao_viens');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_bai_taps');
    }
};
