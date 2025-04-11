<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietKetQua extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_ket_quas';

    protected $fillable = [
        'ket_qua_id',
        'cau_hoi_id',
        'dap_an_id',
        'la_dap_an_dung',
    ];

    protected $casts = [
        'la_dap_an_dung' => 'boolean',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với kết quả bài kiểm tra
     */
    public function ketQua(): BelongsTo
    {
        return $this->belongsTo(KetQuaBaiKiemTra::class, 'ket_qua_id');
    }

    /**
     * Quan hệ với câu hỏi
     */
    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ với đáp án
     */
    public function dapAn(): BelongsTo
    {
        return $this->belongsTo(DapAn::class, 'dap_an_id');
    }
} 