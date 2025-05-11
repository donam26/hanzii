<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuongTroGiang extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'tro_giang_id',
        'lop_hoc_id',
        'so_tien',
        'phan_tram',
        'trang_thai',
        'ngay_thanh_toan',
        'ghi_chu'
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime'
    ];
    
    /**
     * Lấy trợ giảng liên quan đến lương
     */
    public function troGiang()
    {
        return $this->belongsTo(TroGiang::class, 'tro_giang_id');
    }
    
    /**
     * Lấy lớp học liên quan đến lương
     */
    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }
    
    /**
     * Kiểm tra xem lương đã được thanh toán chưa
     */
    public function daThanhToan()
    {
        return $this->trang_thai === 'da_thanh_toan';
    }
}
