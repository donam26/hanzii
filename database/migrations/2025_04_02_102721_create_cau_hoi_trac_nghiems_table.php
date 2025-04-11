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
        Schema::create('cau_hoi_trac_nghiems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_tap_id')->constrained('bai_taps')->onDelete('cascade');
            $table->text('noi_dung');
            $table->text('giai_thich')->nullable();
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hoi_trac_nghiems');
    }
};