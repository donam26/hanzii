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
        Schema::table('bai_hocs', function (Blueprint $table) {
            // Chỉ thêm các trường nếu chưa tồn tại
            if (!Schema::hasColumn('bai_hocs', 'noi_dung')) {
                $table->longText('noi_dung')->nullable()->after('mo_ta');
            }
            if (!Schema::hasColumn('bai_hocs', 'loai')) {
                $table->string('loai')->default('van_ban')->after('noi_dung');
            }
            if (!Schema::hasColumn('bai_hocs', 'url_video')) {
                $table->string('url_video')->nullable()->after('loai');
            }
            if (!Schema::hasColumn('bai_hocs', 'thoi_luong')) {
                $table->integer('thoi_luong')->default(45)->after('url_video');
            }
            if (!Schema::hasColumn('bai_hocs', 'trang_thai')) {
                $table->string('trang_thai')->default('chua_xuat_ban')->after('thoi_luong');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_hocs', function (Blueprint $table) {
            // Chỉ xóa các trường nếu có tồn tại
            if (Schema::hasColumn('bai_hocs', 'noi_dung')) {
                $table->dropColumn('noi_dung');
            }
            if (Schema::hasColumn('bai_hocs', 'loai')) {
                $table->dropColumn('loai');
            }
            if (Schema::hasColumn('bai_hocs', 'url_video')) {
                $table->dropColumn('url_video');
            }
            if (Schema::hasColumn('bai_hocs', 'thoi_luong')) {
                $table->dropColumn('thoi_luong');
            }
            if (Schema::hasColumn('bai_hocs', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
        });
    }
}; 