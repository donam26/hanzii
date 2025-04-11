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
        Schema::table('khoa_hocs', function (Blueprint $table) {
            $table->string('trang_thai')->default('hoat_dong')->after('mo_ta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khoa_hocs', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
}; 