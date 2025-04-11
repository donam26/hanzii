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
        // Chắc chắn rằng các bảng liên quan đã được tạo trước
        if (!Schema::hasTable('ket_qua_trac_nghiems') || 
            !Schema::hasTable('cau_hoi_trac_nghiems') || 
            !Schema::hasTable('lua_chon_cau_hois')) {
            // Không thực hiện nếu các bảng chưa tồn tại
            return;
        }
        
        Schema::create('chi_tiet_cau_tra_loi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ket_qua_id')->constrained('ket_qua_trac_nghiems')->onDelete('cascade');
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi_trac_nghiems')->onDelete('cascade');
            $table->foreignId('lua_chon_da_chon_id')->nullable()->constrained('lua_chon_cau_hois')->onDelete('set null');
            $table->foreignId('lua_chon_dung_id')->constrained('lua_chon_cau_hois')->onDelete('cascade');
            $table->boolean('da_tra_loi')->default(false);
            $table->float('diem')->default(0);
            $table->text('ghi_chu')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_cau_tra_loi');
    }
}; 