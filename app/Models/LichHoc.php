<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichHoc extends Model
{
    use HasFactory;

    protected $table = 'lich_hocs';

    protected $fillable = [
        'lop_hoc_id',
        'bai_hoc_id',
        'ngay_hoc',
        'gio_bat_dau',
        'gio_ket_thuc',
        'noi_dung',
        'link_hoc',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_hoc' => 'date',
        'gio_bat_dau' => 'datetime',
        'gio_ket_thuc' => 'datetime',
    ];

    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với lớp học
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với bài học
     */
    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }
    
    /**
     * Kiểm tra xem lịch học đã diễn ra chưa
     */
    public function isDaHoc(): bool
    {
        return now()->gt($this->gio_ket_thuc);
    }
    
    /**
     * Kiểm tra xem lịch học có đang diễn ra không
     */
    public function isDangHoc(): bool
    {
        return now()->between($this->gio_bat_dau, $this->gio_ket_thuc);
    }
} 