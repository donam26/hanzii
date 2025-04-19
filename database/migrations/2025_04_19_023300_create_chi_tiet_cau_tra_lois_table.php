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
        Schema::create('chi_tiet_cau_tra_lois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_tap_da_nop_id')->constrained('bai_tap_da_nops')->onDelete('cascade');
            $table->foreignId('cau_hoi_id')->constrained('cau_hois')->onDelete('cascade');
            $table->foreignId('dap_an_id')->nullable()->constrained('dap_ans')->onDelete('set null');
            $table->boolean('la_dap_an_dung')->default(false);
            $table->timestamp('tao_luc')->nullable();
            $table->timestamp('cap_nhat_luc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_cau_tra_lois');
    }
};
