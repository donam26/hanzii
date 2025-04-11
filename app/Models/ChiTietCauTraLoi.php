<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietCauTraLoi extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     *
     * @var string
     */
    protected $table = 'chi_tiet_cau_tra_loi';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'ket_qua_bai_kiem_tra_id',
        'cau_hoi_id',
        'dap_an_id',
        'dap_an_dung_id',
        'da_tra_loi',
        'diem',
        'ghi_chu'
    ];

    /**
     * Các thuộc tính cần chuyển đổi
     *
     * @var array
     */
    protected $casts = [
        'da_tra_loi' => 'boolean',
        'diem' => 'float',
    ];

    /**
     * Kết quả bài kiểm tra
     */
    public function ketQuaBaiKiemTra()
    {
        return $this->belongsTo(KetQuaBaiKiemTra::class, 'ket_qua_bai_kiem_tra_id');
    }

    /**
     * Câu hỏi
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    /**
     * Đáp án đã chọn
     */
    public function dapAn()
    {
        return $this->belongsTo(DapAn::class, 'dap_an_id');
    }

    /**
     * Đáp án đúng
     */
    public function dapAnDung()
    {
        return $this->belongsTo(DapAn::class, 'dap_an_dung_id');
    }
}
