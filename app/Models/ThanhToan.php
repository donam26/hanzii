<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'thanh_toans';

    protected $fillable = [
        'dang_ky_id',
        'so_tien',
        'ngay_thanh_toan',
        'phuong_thuc_thanh_toan',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'date',
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
}
