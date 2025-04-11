<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NopBaiTap extends Model
{
    use HasFactory;

    protected $table = 'nop_bai_taps';

    protected $fillable = [
        'dang_ky_id',
        'bai_tap_id',
        'file_path',
        'nhan_xet',
        'diem',
        'ngay_nop',
        'ngay_cham',
        'nguoi_cham_id',
    ];

    protected $casts = [
        'ngay_nop' => 'datetime',
        'ngay_cham' => 'datetime',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với đăng ký học
     */
    public function dangKy(): BelongsTo
    {
        return $this->belongsTo(DangKyHoc::class, 'dang_ky_id');
    }

    /**
     * Quan hệ với bài tập
     */
    public function baiTap(): BelongsTo
    {
        return $this->belongsTo(BaiTap::class, 'bai_tap_id');
    }

    /**
     * Quan hệ với người chấm (giáo viên)
     */
    public function nguoiCham(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'nguoi_cham_id');
    }
} 