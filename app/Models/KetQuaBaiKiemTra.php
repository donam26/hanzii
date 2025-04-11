<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KetQuaBaiKiemTra extends Model
{
    use HasFactory;

    protected $table = 'ket_qua_bai_kiem_tras';

    protected $fillable = [
        'dang_ky_id',
        'bai_kiem_tra_id',
        'so_cau_dung',
        'tong_so_cau',
        'diem',
        'thoi_gian_lam_bai',
        'ngay_lam',
    ];

    protected $casts = [
        'ngay_lam' => 'datetime',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với đăng ký học
     */
    public function dangKyHoc(): BelongsTo
    {
        return $this->belongsTo(DangKyHoc::class, 'dang_ky_id');
    }

    /**
     * Quan hệ với bài kiểm tra
     */
    public function baiKiemTra(): BelongsTo
    {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }

    /**
     * Quan hệ với chi tiết kết quả
     */
    public function chiTietKetQua(): HasMany
    {
        return $this->hasMany(ChiTietKetQua::class, 'ket_qua_id');
    }
} 