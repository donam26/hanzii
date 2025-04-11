<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuongTroGiang extends Model
{
    use HasFactory;

    protected $table = 'luong_tro_giangs';
    protected $fillable = [
        'tro_giang_id',
        'lop_hoc_id',
        'tong_hoc_phi_thu_duoc',
        'vai_tro_id',
        'tong_luong',
        'ngay_thanh_toan',
        'trang_thai',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    public function troGiang()
    {
        return $this->belongsTo(TroGiang::class, 'tro_giang_id');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }
} 