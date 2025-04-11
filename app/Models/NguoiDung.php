<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NguoiDung extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'nguoi_dungs';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'ho',
        'ten',
        'email',
        'so_dien_thoai',
        'mat_khau',
        'loai_tai_khoan',
        'dia_chi',
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ 1-1 với giáo viên
     */
    public function giaoVien(): HasOne
    {
        return $this->hasOne(GiaoVien::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ 1-1 với trợ giảng
     */
    public function troGiang(): HasOne
    {
        return $this->hasOne(TroGiang::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ 1-1 với học viên
     */
    public function hocVien(): HasOne
    {
        return $this->hasOne(HocVien::class, 'nguoi_dung_id');
    }

    /**
     * Quan hệ n-n với vai trò
     */
    public function vaiTros(): BelongsToMany
    {
        return $this->belongsToMany(VaiTro::class, 'vai_tro_nguoi_dungs', 'nguoi_dung_id', 'vai_tro_id')
            ->withTimestamps('tao_luc', 'cap_nhat_luc');
    }

    /**
     * Quan hệ 1-n với bình luận
     */
    public function binhLuans(): HasMany
    {
        return $this->hasMany(BinhLuan::class, 'nguoi_dung_id');
    }

    /**
     * Accessor để lấy họ tên đầy đủ
     */
    public function getHoTenAttribute()
    {
        return "{$this->ho} {$this->ten}";
    }
}
