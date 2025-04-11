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
        Schema::create('dap_ans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cau_hoi_id');
            $table->text('noi_dung');
            $table->boolean('la_dap_an_dung')->default(false);
            $table->timestamp('tao_luc')->nullable();
            $table->timestamp('cap_nhat_luc')->nullable();

            $table->foreign('cau_hoi_id')->references('id')->on('cau_hois')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_ans');
    }
};
