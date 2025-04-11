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
        Schema::create('nop_bai_taps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('bai_tap_id')->constrained('bai_taps')->onDelete('cascade');
            $table->text('noi_dung')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->decimal('diem', 5, 2)->nullable();
            $table->text('nhan_xet')->nullable();
            $table->enum('trang_thai', ['da_nop', 'dang_cham', 'da_cham', 'yeu_cau_nop_lai'])->default('da_nop');
            $table->foreignId('nguoi_cham_id')->nullable()->constrained('giao_viens')->onDelete('set null');
            $table->timestamp('thoi_gian_nop')->useCurrent();
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
        Schema::dropIfExists('nop_bai_taps');
    }
}; 