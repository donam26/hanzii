<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuaChonCauHoi extends Model
{
    use HasFactory;

    protected $table = 'lua_chon_cau_hois';

    protected $fillable = [
        'cau_hoi_id',
        'noi_dung_lua_chon',
        'la_dap_an_dung',
        'so_thu_tu',
    ];

    protected $casts = [
        'la_dap_an_dung' => 'boolean',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với câu hỏi trắc nghiệm
     */
    public function cauHoiTracNghiem(): BelongsTo
    {
        return $this->belongsTo(CauHoiTracNghiem::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ với đáp án trắc nghiệm
     */
    public function dapAnTracNghiems(): HasMany
    {
        return $this->hasMany(DapAnTracNghiem::class, 'lua_chon_da_chon_id');
    }
}
