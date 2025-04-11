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
        Schema::table('bai_taps', function (Blueprint $table) {
            $table->timestamp('han_nop')->nullable()->after('diem_toi_da');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_taps', function (Blueprint $table) {
            $table->dropColumn('han_nop');
        });
    }
};
