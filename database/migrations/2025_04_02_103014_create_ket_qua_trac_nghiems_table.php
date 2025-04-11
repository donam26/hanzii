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
        Schema::create('ket_qua_trac_nghiems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('bai_tap_id')->constrained('bai_taps')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->float('diem')->default(0);
            $table->timestamp('ngay_nop')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_trac_nghiems');
    }
};
