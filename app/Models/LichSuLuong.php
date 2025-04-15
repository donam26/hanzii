<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuLuong extends Model
{
    use HasFactory;

    protected $table = 'lich_su_luongs';

    protected $fillable = [
        'luong_id',
        'giao_vien_id',
        'so_tien_cu',
        'so_tien_moi',
        'nguoi_cap_nhat',
        'ly_do',
        'ghi_chu'
    ];

    protected $casts = [
        'so_tien_cu' => 'float',
        'so_tien_moi' => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Lấy thông tin bảng lương
     */
    public function luong()
    {
        return $this->belongsTo(Luong::class, 'luong_id');
    }

    /**
     * Lấy thông tin giáo viên
     */
    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    /**
     * Lấy thông tin người cập nhật
     */
    public function nguoiCapNhat()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_cap_nhat');
    }
} 