<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CauHoiTracNghiem extends Model
{
    use HasFactory;

    protected $table = 'cau_hoi_trac_nghiems';

    protected $fillable = [
        'bai_tap_id',
        'noi_dung',
        'giai_thich',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với bài tập
     */
    public function baiTap(): BelongsTo
    {
        return $this->belongsTo(BaiTap::class, 'bai_tap_id');
    }

    /**
     * Quan hệ với lựa chọn câu hỏi
     */
    public function luaChonCauHois(): HasMany
    {
        return $this->hasMany(LuaChonCauHoi::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ với đáp án trắc nghiệm
     */
    public function dapAnTracNghiems(): HasMany
    {
        return $this->hasMany(DapAnTracNghiem::class, 'cau_hoi_id');
    }

    /**
     * Lấy đáp án đúng
     */
    public function dapAnDung()
    {
        return $this->luaChonCauHois()->where('la_dap_an_dung', true)->first();
    }
}
