<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMucKhoaHoc extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'danh_muc_khoa_hocs';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'ten',
        'mo_ta',
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
     * Quan hệ với khóa học
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function khoaHocs(): HasMany
    {
        return $this->hasMany(KhoaHoc::class, 'danh_muc_id');
    }
} 