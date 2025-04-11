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
        Schema::create('lich_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->foreignId('bai_hoc_id')->nullable()->constrained('bai_hocs')->onDelete('set null');
            $table->date('ngay_hoc');
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->text('noi_dung')->nullable();
            $table->string('link_hoc')->nullable();
            $table->enum('trang_thai', ['da_len_lich', 'dang_hoc', 'da_hoc', 'huy_hoc'])->default('da_len_lich');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hocs');
    }
}; 