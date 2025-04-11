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
        Schema::create('thanh_toans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dang_ky_id')->unique()->constrained('dang_ky_hocs')->onDelete('cascade');
            $table->float('so_tien')->default(0);
            $table->date('ngay_thanh_toan');
            $table->string('phuong_thuc_thanh_toan')->nullable();
            $table->string('trang_thai')->default('cho_thanh_toan')->comment('cho_thanh_toan, da_thanh_toan, da_hoan_tien');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toans');
    }
};
