<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuongGiaoVien extends Model
{
    use HasFactory;

    protected $table = 'luong_giao_viens';

    protected $fillable = [
        'giao_vien_id',
        'lop_hoc_id',
        'tong_hoc_phi_thu_duoc',
        'vai_tro_id',
        'tong_luong',
        'ngay_thanh_toan',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'date',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với giáo viên
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với vai trò
     */
    public function vaiTro(): BelongsTo
    {
        return $this->belongsTo(VaiTro::class, 'vai_tro_id');
    }
} 