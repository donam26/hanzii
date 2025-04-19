<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Luong extends Model
{
    use HasFactory;

    protected $table = 'luongs';

    // Constants for payment status
    const TRANG_THAI_CHO_THANH_TOAN = 0;
    const TRANG_THAI_DA_THANH_TOAN = 1;
    const TRANG_THAI_HUY = 2;

    protected $fillable = [
        'nguoi_dung_id',
        'lop_hoc_id',
        'vai_tro',
        'phan_tram',
        'so_tien',
        'trang_thai',
        'ngay_thanh_toan',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime',
    ];

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Lấy thông tin giáo viên
     */
    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    /**
     * Lấy thông tin vai trò (giáo viên chính/phụ)
     */
    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }

    /**
     * Lấy thông tin người thanh toán
     */
    public function nguoiThanhToan()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_thanh_toan');
    }

    /**
     * Lấy lịch sử cập nhật lương
     */
    public function lichSuLuong()
    {
        return $this->hasMany(LichSuLuong::class, 'luong_id');
    }
} 