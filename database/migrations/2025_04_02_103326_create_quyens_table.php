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
        Schema::create('quyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('ma_quyen')->unique()->comment('Mã định danh duy nhất, ví dụ: xem_nguoi_dung, sua_khoa_hoc');
            $table->string('mo_ta')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyens');
    }
};
