<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaiTapDaNop extends Model
{
    use HasFactory;

    protected $table = 'bai_tap_da_nops';

    protected $fillable = [
        'bai_tap_id',
        'hoc_vien_id',
        'noi_dung',
        'file_path',
        'ten_file',
        'diem',
        'trang_thai',
        'ngay_nop',
        'phan_hoi',
    ];

    /**
     * Quan hệ với bài tập
     */
    public function baiTap(): BelongsTo
    {
        return $this->belongsTo(BaiTap::class, 'bai_tap_id');
    }

    /**
     * Quan hệ với học viên
     */
    public function hocVien(): BelongsTo
    {
        return $this->belongsTo(HocVien::class, 'hoc_vien_id');
    }
}
