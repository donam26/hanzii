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
        Schema::table('tro_giangs', function (Blueprint $table) {
            $table->string('chuyen_mon')->nullable()->comment('Chuyên môn giảng dạy, ví dụ: hsk1,hsk2,hsk3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tro_giangs', function (Blueprint $table) {
            $table->dropColumn('chuyen_mon');
        });
    }
};
