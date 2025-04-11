<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YeuCauThamGia extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'yeu_cau_tham_gia';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'lop_hoc_id',
        'hoc_vien_id',
        'trang_thai',
        'ghi_chu',
        'nguoi_duyet_id',
        'ly_do_tu_choi',
        'ngay_duyet',
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với lớp học
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với học viên
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'hoc_vien_id');
    }

    /**
     * Quan hệ với người duyệt (giáo viên)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_duyet_id');
    }
} 