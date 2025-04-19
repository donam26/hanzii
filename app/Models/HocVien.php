<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HocVien extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'hoc_viens';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'nguoi_dung_id',
        'trinh_do_hoc_van',
        'ngay_sinh',
        'trang_thai',
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với người dùng
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ với đăng ký học
     */
    public function dangKyHocs(): HasMany
    {
        return $this->hasMany(DangKyHoc::class, 'hoc_vien_id');
    }

    /**
     * Quan hệ với tiến độ bài học
     */
    public function tienDoBaiHocs(): HasMany
    {
        return $this->hasMany(TienDoBaiHoc::class, 'hoc_vien_id');
    }


    /**
     * Quan hệ với bài tập tự luận
     */
    public function baiTuLuans(): HasMany
    {
        return $this->hasMany(BaiTuLuan::class, 'hoc_vien_id');
    }

    /**
     * Quan hệ với file bài tập
     */
    public function fileBaiTaps(): HasMany
    {
        return $this->hasMany(FileBaiTap::class, 'hoc_vien_id');
    }

    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ nhiều-nhiều với lớp học thông qua đăng ký học
     */
    public function lopHocs(): BelongsToMany
    {
        return $this->belongsToMany(LopHoc::class, 'dang_ky_hocs', 'hoc_vien_id', 'lop_hoc_id')
                    ->whereIn('dang_ky_hocs.trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
                    ->withPivot(['ngay_dang_ky', 'trang_thai'])
                    ->withTimestamps('tao_luc', 'cap_nhat_luc');
    }
}
