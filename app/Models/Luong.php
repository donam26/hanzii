<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Luong extends Model
{
    use HasFactory;

    protected $table = 'luongs';

    // Constants for payment status
    const TRANG_THAI_CHO_THANH_TOAN = 0;
    const TRANG_THAI_DA_THANH_TOAN = 1;
    const TRANG_THAI_HUY = 2;

    protected $fillable = [
        'giao_vien_id',
        'lop_hoc_id',
        'vai_tro_id',
        'he_so_luong',
        'tong_luong',
        'thang',
        'nam',
        'trang_thai',
        'nguoi_thanh_toan',
        'ngay_thanh_toan',
        'ghi_chu'
    ];

    protected $casts = [
        'he_so_luong' => 'float',
        'tong_luong' => 'float',
        'ngay_thanh_toan' => 'datetime',
    ];

    /**
     * Lấy thông tin giáo viên
     */
    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    /**
     * Lấy thông tin lớp học
     */
    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
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