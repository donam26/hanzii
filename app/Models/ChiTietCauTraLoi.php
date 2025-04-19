<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietCauTraLoi extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     *
     * @var string
     */
    protected $table = 'chi_tiet_cau_tra_lois';

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'bai_tap_da_nop_id',
        'cau_hoi_id',
        'dap_an_id',
        'la_dap_an_dung',
    ];

    /**
     * Các thuộc tính cần chuyển đổi
     *
     * @var array
     */
    protected $casts = [
        'la_dap_an_dung' => 'boolean',
    ];

    /**
     * Quan hệ với bài tập đã nộp
     */
    public function baiTapDaNop(): BelongsTo
    {
        return $this->belongsTo(BaiTapDaNop::class, 'bai_tap_da_nop_id');
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
