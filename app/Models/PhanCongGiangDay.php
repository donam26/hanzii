<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhanCongGiangDay extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'phan_cong_giang_days';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'lop_hoc_id',
        'tro_giang_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
        'ghi_chu',
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Các cột cần convert thành kiểu dữ liệu khác
     *
     * @var array
     */
    protected $casts = [
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với trợ giảng
     */
    public function troGiang(): BelongsTo
    {
        return $this->belongsTo(TroGiang::class, 'tro_giang_id');
    }
} 