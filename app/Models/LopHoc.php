<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LopHoc extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'lop_hocs';

    /**
     * Các cột có thể gán giá trị
     *
     * @var array
     */
    protected $fillable = [
        'ten',
        'ma_lop',
        'khoa_hoc_id',
        'giao_vien_id',
        'tro_giang_id',
        'hinh_thuc_hoc',
        'lich_hoc',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    /**
     * Các cột thời gian tùy chỉnh
     *
     * @var array
     */
    const CREATED_AT = 'tao_luc';
    const UPDATED_AT = 'cap_nhat_luc';

    /**
     * Quan hệ với khóa học
     */
    public function khoaHoc(): BelongsTo
    {
        return $this->belongsTo(KhoaHoc::class, 'khoa_hoc_id');
    }

    /**
     * Quan hệ với giáo viên
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    /**
     * Quan hệ với trợ giảng
     */
    public function troGiang(): BelongsTo
    {
        return $this->belongsTo(TroGiang::class, 'tro_giang_id');
    }

    /**
     * Quan hệ với đăng ký học
     */
    public function dangKyHocs(): HasMany
    {
        return $this->hasMany(DangKyHoc::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với bài học lớp
     */
    public function baiHocLops(): HasMany
    {
        return $this->hasMany(BaiHocLop::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với bài học (nhiều-nhiều)
     */
    public function baiHocs(): BelongsToMany
    {
        return $this->belongsToMany(BaiHoc::class, 'bai_hoc_lops', 'lop_hoc_id', 'bai_hoc_id')
            ->withPivot(['so_thu_tu', 'ngay_bat_dau', 'trang_thai'])
            ->withTimestamps('tao_luc', 'cap_nhat_luc');
    }

    /**
     * Quan hệ với tài liệu bổ trợ
     */
    public function taiLieuBoTros(): HasMany
    {
        return $this->hasMany(TaiLieuBoTro::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với lương giáo viên
     */
    public function luongGiaoViens(): HasMany
    {
        return $this->hasMany(LuongGiaoVien::class, 'lop_hoc_id');
    }

    /**
     * Quan hệ với lương trợ giảng
     */
    public function luongTroGiangs(): HasMany
    {
        return $this->hasMany(LuongTroGiang::class, 'lop_hoc_id');
    }

    /**
     * Lấy danh sách học viên của lớp
     */
    public function hocViens()
    {
        return $this->belongsToMany(HocVien::class, 'dang_ky_hocs', 'lop_hoc_id', 'hoc_vien_id')
            ->whereIn('dang_ky_hocs.trang_thai', ['dang_hoc', 'da_duyet'])
            ->withPivot(['ngay_tham_gia', 'ngay_dang_ky', 'trang_thai'])
            ->withTimestamps('tao_luc', 'cap_nhat_luc');
    }

    /**
     * Tính toán tiến độ của lớp học dựa trên ngày bắt đầu và ngày kết thúc
     * 
     * @return int Phần trăm tiến độ (0-100)
     */
    public function tienDo()
    {
        // Nếu lớp chưa bắt đầu
        if ($this->ngay_bat_dau > now()) {
            return 0;
        }
        
        // Nếu lớp đã kết thúc
        if ($this->ngay_ket_thuc < now()) {
            return 100;
        }
        
        // Tính toán tiến độ dựa trên ngày bắt đầu và ngày kết thúc
        $ngayBatDau = \Carbon\Carbon::parse($this->ngay_bat_dau);
        $ngayKetThuc = \Carbon\Carbon::parse($this->ngay_ket_thuc);
        $tongSoNgay = $ngayBatDau->diffInDays($ngayKetThuc) ?: 1; // Tránh chia cho 0
        $ngayDaQua = $ngayBatDau->diffInDays(now());
        
        // Đảm bảo tiến độ từ 0-100%
        $tienDo = min(100, max(0, round(($ngayDaQua / $tongSoNgay) * 100)));
        
        return $tienDo;
    }
}
