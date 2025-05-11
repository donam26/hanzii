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
        Schema::create('thanh_toan_hoc_phis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_vien_id')->constrained('hoc_viens')->onDelete('cascade');
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->decimal('so_tien', 12, 0);
            $table->enum('trang_thai', ['chua_thanh_toan', 'da_thanh_toan'])->default('chua_thanh_toan');
            $table->string('ma_thanh_toan', 20)->unique();
            $table->text('ghi_chu')->nullable();
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toan_hoc_phis');
    }
};
