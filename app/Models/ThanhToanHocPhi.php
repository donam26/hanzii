<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThanhToanHocPhi extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'hoc_vien_id',
        'lop_hoc_id',
        'so_tien',
        'trang_thai',
        'ma_thanh_toan',
        'ghi_chu',
        'ngay_thanh_toan'
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime'
    ];
    
    /**
     * Lấy học viên liên quan đến thanh toán
     */
    public function hocVien()
    {
        return $this->belongsTo(HocVien::class, 'hoc_vien_id');
    }
    
    /**
     * Lấy lớp học liên quan đến thanh toán
     */
    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }
    
    /**
     * Kiểm tra xem thanh toán đã hoàn thành chưa
     */
    public function daThanhToan()
    {
        return $this->trang_thai === 'da_thanh_toan';
    }
}
