<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongKeTaiChinh extends Model
{
    use HasFactory;

    protected $table = 'thong_ke_tai_chinhs';

    protected $fillable = [
        'thang',
        'nam',
        'tong_thu',
        'tong_chi',
        'loi_nhuan',
        'ghi_chu',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Tạo thống kê tài chính từ dữ liệu
     */
    public static function taoThongKe($thang, $nam)
    {
        // Logic tạo thống kê tài chính từ lương và thanh toán
        $tongThu = ThanhToan::whereYear('ngay_thanh_toan', $nam)
            ->whereMonth('ngay_thanh_toan', $thang)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');

        $tongChi = LuongGiaoVien::whereYear('ngay_thanh_toan', $nam)
            ->whereMonth('ngay_thanh_toan', $thang)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('tong_luong');

        $loiNhuan = $tongThu - $tongChi;

        return self::updateOrCreate(
            ['thang' => $thang, 'nam' => $nam],
            [
                'tong_thu' => $tongThu,
                'tong_chi' => $tongChi,
                'loi_nhuan' => $loiNhuan,
            ]
        );
    }
}
