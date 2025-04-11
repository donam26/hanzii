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
        Schema::create('cau_hois', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bai_tap_id');
            $table->text('noi_dung');
            $table->decimal('diem', 5, 2)->default(1);
            $table->timestamp('tao_luc')->nullable();
            $table->timestamp('cap_nhat_luc')->nullable();

            $table->foreign('bai_tap_id')->references('id')->on('bai_taps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hois');
    }
};
