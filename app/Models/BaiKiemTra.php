<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaiKiemTra extends Model
{
    use HasFactory;

    protected $table = 'bai_kiem_tras';

    protected $fillable = [
        'lop_hoc_id',
        'ten',
        'mo_ta',
        'thoi_gian_lam',
        'so_luong_cau_hoi',
        'diem_toi_da',
        'trang_thai',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với câu hỏi của bài kiểm tra
     */
    public function cauHois(): HasMany
    {
        return $this->hasMany(CauHoi::class, 'bai_kiem_tra_id');
    }

    /**
     * Quan hệ với kết quả bài kiểm tra
     */
    public function ketQuaBaiKiemTras(): HasMany
    {
        return $this->hasMany(KetQuaBaiKiemTra::class, 'bai_kiem_tra_id');
    }
} 