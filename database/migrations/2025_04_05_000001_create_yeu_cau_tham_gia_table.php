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
        Schema::create('yeu_cau_tham_gia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->timestamp('ngay_dang_ky')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('nguoi_dungs');
            $table->text('ly_do_tu_choi')->nullable();
            $table->timestamp('ngay_duyet')->nullable();
            $table->timestamp('ngay_gui')->useCurrent();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->nullable()->useCurrentOnUpdate();
            
            // Đảm bảo một học viên chỉ được gửi một yêu cầu đến một lớp học
            $table->unique(['lop_hoc_id', 'hoc_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_tham_gia');
    }
}; 