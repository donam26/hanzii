<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoaDon extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'hoa_dons';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'thanh_toan_id',
        'ma_hoa_don',
        'hoc_vien_id',
        'lop_hoc_id',
        'tong_tien',
        'trang_thai',
        'ngay_tao',
        'ghi_chu'
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với thanh toán
     */
    public function thanhToan(): BelongsTo
    {
        return $this->belongsTo(ThanhToan::class, 'thanh_toan_id');
    }

    /**
     * Quan hệ với học viên
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'hoc_vien_id');
    }

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }
}
