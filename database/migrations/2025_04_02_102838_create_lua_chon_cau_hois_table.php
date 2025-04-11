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
        Schema::create('lua_chon_cau_hois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi_trac_nghiems')->onDelete('cascade');
            $table->text('noi_dung_lua_chon');
            $table->boolean('la_dap_an_dung')->default(false)->comment('Lựa chọn đúng hay không');
            $table->integer('so_thu_tu')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lua_chon_cau_hois');
    }
};
