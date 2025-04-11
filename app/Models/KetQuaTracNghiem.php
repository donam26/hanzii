<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KetQuaTracNghiem extends Model
{
    use HasFactory;

    protected $table = 'ket_qua_trac_nghiems';

    protected $fillable = [
        'hoc_vien_id',
        'bai_tap_id',
        'lop_hoc_id',
        'diem',
        'ngay_nop',
    ];

    protected $casts = [
        'ngay_nop' => 'datetime',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với học viên
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'hoc_vien_id');
    }

    /**
     * Quan hệ với bài tập
     */
    public function baiTap(): BelongsTo
    {
        return $this->belongsTo(BaiTap::class, 'bai_tap_id');
    }

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với đáp án trắc nghiệm
     */
    public function dapAnTracNghiems(): HasMany
    {
        return $this->hasMany(DapAnTracNghiem::class, 'ket_qua_id');
    }
}
