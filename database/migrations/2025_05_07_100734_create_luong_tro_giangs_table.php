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
        Schema::create('luong_tro_giangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tro_giang_id')->constrained('tro_giangs')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->decimal('so_tien', 12, 0);
            $table->integer('phan_tram')->default(15); // 15% mặc định
            $table->enum('trang_thai', ['chua_thanh_toan', 'da_thanh_toan'])->default('chua_thanh_toan');
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luong_tro_giangs');
    }
};
