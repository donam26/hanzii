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
        Schema::create('dap_an_trac_nghiems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ket_qua_id')->constrained('ket_qua_trac_nghiems')->onDelete('cascade');
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi_trac_nghiems')->onDelete('cascade');
            $table->foreignId('lua_chon_da_chon_id')->constrained('lua_chon_cau_hois')->onDelete('cascade');
            $table->boolean('la_dap_an_dung')->default(false);
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_an_trac_nghiems');
    }
};
