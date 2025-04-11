<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DapAn extends Model
{
    use HasFactory;

    protected $table = 'dap_ans';

    protected $fillable = [
        'cau_hoi_id',
        'noi_dung',
        'la_dap_an_dung',
        'hinh_anh',
        'thu_tu',
    ];

    protected $casts = [
        'la_dap_an_dung' => 'boolean',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với câu hỏi
     */
    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ với chi tiết kết quả
     */
    public function chiTietKetQuas(): HasMany
    {
        return $this->hasMany(ChiTietKetQua::class, 'dap_an_id');
    }
} 