<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThanhToan extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'thanh_toans';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'hoc_vien_id',
        'dang_ky_hoc_id',
        'ma_thanh_toan',
        'ma_giao_dich',
        'so_tien',
        'noi_dung',
        'phuong_thuc',
        'trang_thai',
        'ngay_thanh_toan',
        'mo_ta'
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
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
     * Quan hệ với đăng ký học
     */
    public function dangKyHoc(): BelongsTo
    {
        return $this->belongsTo(DangKyHoc::class, 'dang_ky_hoc_id');
    }

    /**
     * Quan hệ với hóa đơn
     */
    public function hoaDon(): HasOne
    {
        return $this->hasOne(HoaDon::class, 'thanh_toan_id');
    }
}
