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
        Schema::create('quyen_vai_tros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vai_tro_id')->constrained('vai_tros')->onDelete('cascade');
            $table->foreignId('quyen_id')->constrained('quyens')->onDelete('cascade');
            $table->timestamp('tao_luc')->useCurrent();
            $table->timestamp('cap_nhat_luc')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyen_vai_tros');
    }
};
