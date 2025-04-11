<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaiLieuBoTro extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'tai_lieu_bo_tro';

    /**
     * Các trường có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'lop_hoc_id',
        'bai_hoc_id',
        'ten_tai_lieu',
        'mo_ta',
        'duong_dan_file',
        'loai_tai_lieu',
        'kich_thuoc',
    ];

    /**
     * Các trường sẽ được chuyển đổi thành kiểu dữ liệu cụ thể
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với bài học
     */
    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }
}
