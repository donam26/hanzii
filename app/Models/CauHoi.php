<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CauHoi extends Model
{
    use HasFactory;

    protected $table = 'cau_hois';

    protected $fillable = [
        'bai_tap_id',
        'noi_dung',
        'diem',
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
     * Quan hệ với đáp án
     */
    public function dapAns(): HasMany
    {
        return $this->hasMany(DapAn::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ với chi tiết kết quả
     */
    public function chiTietKetQuas(): HasMany
    {
        return $this->hasMany(ChiTietKetQua::class, 'cau_hoi_id');
    }
} 