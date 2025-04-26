<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HocVien\DashboardController as HocVienDashboardController;
use App\Http\Controllers\HocVien\LopHocController as HocVienLopHocController;
use App\Http\Controllers\HocVien\BaiHocController as HocVienBaiHocController;
use App\Http\Controllers\HocVien\BaiTapController as HocVienBaiTapController;
use App\Http\Controllers\HocVien\KetQuaController as HocVienKetQuaController;
use App\Http\Controllers\GiaoVien\DashboardController as GiaoVienDashboardController;
use App\Http\Controllers\TroGiang\DashboardController as TroGiangDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Học viên routes
Route::prefix('hoc-vien')->name('hoc-vien.')->middleware(['auth', 'role:hoc_vien'])->group(function () {
    Route::get('/dashboard', [HocVienDashboardController::class, 'index'])->name('dashboard');
    
    // Lớp học
    Route::prefix('lop-hoc')->name('lop-hoc.')->group(function () {
        Route::get('/', [App\Http\Controllers\HocVien\LopHocController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\HocVien\LopHocController::class, 'show'])->name('show');
        Route::get('/{id}/tien-do', [App\Http\Controllers\HocVien\LopHocController::class, 'progress'])->name('progress');
        
        // Tìm kiếm lớp học và gửi yêu cầu tham gia
        Route::get('/tim-kiem/form', [App\Http\Controllers\HocVien\LopHocController::class, 'formTimKiem'])->name('form-tim-kiem');
        Route::post('/tim-kiem', [App\Http\Controllers\HocVien\LopHocController::class, 'timKiem'])->name('tim-kiem');
        Route::post('/gui-yeu-cau', [App\Http\Controllers\HocVien\LopHocController::class, 'guiYeuCau'])->name('gui-yeu-cau');
        Route::get('/yeu-cau', [App\Http\Controllers\HocVien\LopHocController::class, 'danhSachYeuCau'])->name('yeu-cau');
    });
    
    // Khóa học
    Route::get('/khoa-hoc', [App\Http\Controllers\HocVien\KhoaHocController::class, 'index'])->name('khoa-hoc.index');
    Route::get('/khoa-hoc/{id}', [App\Http\Controllers\HocVien\KhoaHocController::class, 'show'])->name('khoa-hoc.show');
    Route::get('/khoa-hoc-da-dang-ky', [App\Http\Controllers\HocVien\KhoaHocController::class, 'daDangKy'])->name('khoa-hoc.da-dang-ky');
    
    // Bài học
    Route::get('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}', [HocVienBaiHocController::class, 'show'])->name('bai-hoc.show');
    Route::post('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/cap-nhat-tien-do', [HocVienBaiHocController::class, 'capNhatTienDo'])->name('bai-hoc.cap-nhat-tien-do');
    Route::get('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/bai-tap/{baiTapId}/nop-bai', [HocVienBaiHocController::class, 'formNopBaiTap'])->name('bai-hoc.form-nop-bai-tap');
    Route::post('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/bai-tap/{baiTapId}/nop-bai', [HocVienBaiHocController::class, 'nopBaiTap'])->name('bai-hoc.nop-bai-tap');
    
    // Bài tập
    Route::prefix('bai-tap')->name('bai-tap.')->group(function () {
        Route::get('/{id}', [App\Http\Controllers\HocVien\BaiTapController::class, 'show'])->name('show');
        Route::get('/{id}/lam-bai-trac-nghiem', [App\Http\Controllers\HocVien\BaiTapController::class, 'lamBaiTracNghiem'])->name('lam-bai-trac-nghiem');
        Route::post('/{id}/lam-bai-trac-nghiem', [App\Http\Controllers\HocVien\BaiTapController::class, 'xuLyLamBaiTracNghiem'])->name('xu-ly-lam-bai-trac-nghiem');
        Route::get('/{id}/nop-bai', [App\Http\Controllers\HocVien\BaiTapController::class, 'formNopBai'])->name('form-nop-bai');
        Route::post('/{id}/nop-bai', [App\Http\Controllers\HocVien\BaiTapController::class, 'nopBai'])->name('nop-bai');
        Route::get('/ket-qua/{id}', [App\Http\Controllers\HocVien\BaiTapController::class, 'ketQua'])->name('ket-qua');
    });
    
    // Tài liệu
    Route::get('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/tai-lieu/{taiLieuId}', [HocVienBaiHocController::class, 'taiTaiLieu'])->name('bai-hoc.tai-tai-lieu');
    
    // Kết quả học tập
    Route::get('/ket-qua-hoc-tap', [HocVienKetQuaController::class, 'index'])->name('ket-qua.index');
    Route::get('/ket-qua', [HocVienKetQuaController::class, 'index'])->name('ket-qua');
    Route::get('/ket-qua/{id}', [HocVienKetQuaController::class, 'show'])->name('ket-qua.show');
    
    // Thông tin cá nhân
    Route::get('/profile', [App\Http\Controllers\HocVien\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\HocVien\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\HocVien\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [App\Http\Controllers\HocVien\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [App\Http\Controllers\HocVien\ProfileController::class, 'changePassword'])->name('profile.update-password');
    
    // Bình luận
    Route::post('/binh-luan', [App\Http\Controllers\HocVien\BinhLuanController::class, 'store'])->name('binh-luan.store');
    Route::delete('/binh-luan/{id}', [App\Http\Controllers\HocVien\BinhLuanController::class, 'destroy'])->name('binh-luan.destroy');
    
    // Thanh toán học phí
    Route::prefix('thanh-toan')->name('thanh-toan.')->group(function() {
        Route::get('/', [App\Http\Controllers\HocVien\ThanhToanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\HocVien\ThanhToanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\HocVien\ThanhToanController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\HocVien\ThanhToanController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [App\Http\Controllers\HocVien\ThanhToanController::class, 'cancel'])->name('cancel');
    });
    
    // VNPay
    Route::prefix('vnpay')->name('vnpay.')->group(function() {
        Route::get('/create', [App\Http\Controllers\HocVien\VNPayController::class, 'create'])->name('create');
        Route::get('/return', [App\Http\Controllers\HocVien\VNPayController::class, 'return'])->name('return');
    });
    
    // Thông báo lớp học
    Route::get('/thong-bao', [App\Http\Controllers\HocVien\ThongBaoController::class, 'index'])->name('thong-bao.index');
    Route::get('/thong-bao/{id}', [App\Http\Controllers\HocVien\ThongBaoController::class, 'show'])->name('thong-bao.show');
    Route::post('/thong-bao/mark-all-as-read', [App\Http\Controllers\HocVien\ThongBaoController::class, 'markAllAsRead'])->name('thong-bao.mark-all-as-read');
    Route::get('/thong-bao/count-unread', [App\Http\Controllers\HocVien\ThongBaoController::class, 'countUnread'])->name('thong-bao.count-unread');
});

// Giáo viên routes
Route::prefix('giao-vien')->name('giao-vien.')->middleware(['auth', 'role:giao_vien'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\GiaoVien\DashboardController@index')->name('dashboard');
    
    // Quản lý lớp học
    Route::get('/lop-hoc', 'App\Http\Controllers\GiaoVien\LopHocController@index')->name('lop-hoc.index');
    Route::get('/lop-hoc/{id}', 'App\Http\Controllers\GiaoVien\LopHocController@show')->name('lop-hoc.show');
    Route::get('/lop-hoc/{id}/danh-sach-hoc-vien', 'App\Http\Controllers\GiaoVien\LopHocController@danhSachHocVien')->name('lop-hoc.danh-sach-hoc-vien');
    Route::get('/lop-hoc/{id}/lich-day', 'App\Http\Controllers\GiaoVien\LopHocController@lichDay')->name('lop-hoc.lich-day');
    Route::get('/lop-hoc/{id}/ket-qua', 'App\Http\Controllers\GiaoVien\LopHocController@ketQua')->name('lop-hoc.ket-qua');
    Route::get('/lop-hoc/{id}/add-student', 'App\Http\Controllers\GiaoVien\LopHocController@addStudentForm')->name('lop-hoc.add-student-form');
    Route::post('/lop-hoc/{id}/add-student', 'App\Http\Controllers\GiaoVien\LopHocController@addStudent')->name('lop-hoc.add-student');
    Route::post('/lop-hoc/{id}/add-student-email', 'App\Http\Controllers\GiaoVien\LopHocController@addStudentByEmail')->name('lop-hoc.add-student-email');
    Route::delete('/lop-hoc/{id}/remove-student/{hocVienId}', 'App\Http\Controllers\GiaoVien\LopHocController@removeStudent')->name('lop-hoc.remove-student');
    
    Route::get('lich-day', 'App\Http\Controllers\GiaoVien\LopHocController@lichDay')->name('lich-day');
    Route::post('lop-hoc/{id}/hoc-vien/add-by-email', 'App\Http\Controllers\GiaoVien\LopHocController@addStudentByEmail')->name('lop-hoc.add-student-by-email');
    Route::get('lop-hoc/{id}/ket-qua', 'App\Http\Controllers\GiaoVien\LopHocController@ketQua')->name('lop-hoc.ket-qua');
    
    // Quản lý học viên
    // Quản lý học viên
    Route::get('/hoc-vien', 'App\Http\Controllers\GiaoVien\HocVienController@index')->name('hoc-vien.index');
    Route::get('/hoc-vien/{id}', 'App\Http\Controllers\GiaoVien\HocVienController@show')->name('hoc-vien.show');
    Route::put('/dang-ky-hoc/{id}/xac-nhan', 'App\Http\Controllers\GiaoVien\HocVienController@xacNhan')->name('hoc-vien.xac-nhan');
    
    // Quản lý bài học
    Route::resource('bai-hoc', 'App\Http\Controllers\GiaoVien\BaiHocController');
    
    // Quản lý bài tập
    Route::resource('bai-tap', 'App\Http\Controllers\GiaoVien\BaiTapController');
    
    // Quản lý chấm điểm
    Route::get('/cham-diem', 'App\Http\Controllers\GiaoVien\ChamDiemController@index')->name('cham-diem.index');
    Route::get('/cham-diem/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@show')->name('cham-diem.show');
    Route::post('/cham-diem/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@cham')->name('cham-diem.cham');
    Route::get('/cham-diem/download/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@downloadFile')->name('cham-diem.download');
    Route::post('/cham-diem/cap-nhat-trang-thai/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@capNhatTrangThai')->name('cham-diem.cap-nhat-trang-thai');
    Route::get('/cham-diem/tu-luan/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@tuLuan')->name('cham-diem.tu-luan');
    Route::post('/cham-diem/yeu-cau-nop-lai/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@yeuCauNopLai')->name('cham-diem.yeu-cau-nop-lai');
    
    // Quản lý yêu cầu tham gia
    Route::prefix('yeu-cau-tham-gia')->name('yeu-cau-tham-gia.')->group(function () {
        Route::get('/', [App\Http\Controllers\GiaoVien\YeuCauThamGiaController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\GiaoVien\YeuCauThamGiaController::class, 'show'])->name('show');
        Route::post('/{id}/phe-duyet', [App\Http\Controllers\GiaoVien\YeuCauThamGiaController::class, 'duyet'])->name('duyet');
        Route::post('/{id}/tu-choi', [App\Http\Controllers\GiaoVien\YeuCauThamGiaController::class, 'tuChoi'])->name('tu-choi');
    });
    
    // Thêm các route mới cho xác nhận và từ chối học viên
    Route::post('/{id}/xac-nhan-hoc-vien/{dangKyId}', 'App\Http\Controllers\GiaoVien\LopHocController@xacNhanHocVien')->name('xac-nhan-hoc-vien');
    Route::post('/{id}/tu-choi-hoc-vien/{dangKyId}', 'App\Http\Controllers\GiaoVien\LopHocController@tuChoiHocVien')->name('tu-choi-hoc-vien');
    
    // Bình luận
    Route::prefix('binh-luan')->name('binh-luan.')->group(function () {
        Route::get('/', [App\Http\Controllers\GiaoVien\BinhLuanController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\GiaoVien\BinhLuanController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\GiaoVien\BinhLuanController::class, 'destroy'])->name('destroy');
    });
    
    // Lương
    Route::get('/luong', [App\Http\Controllers\GiaoVien\LuongController::class, 'index'])->name('luong.index');
    Route::get('/luong/{id}', [App\Http\Controllers\GiaoVien\LuongController::class, 'show'])->name('luong.show');
    
    // Thông báo lớp học
    Route::resource('thong-bao', App\Http\Controllers\GiaoVien\ThongBaoController::class);
    Route::get('/thong-bao/{id}/delete-file', [App\Http\Controllers\GiaoVien\ThongBaoController::class, 'deleteFile'])->name('thong-bao.delete-file');
});

// Trợ giảng routes
Route::prefix('tro-giang')->name('tro-giang.')->middleware(['auth', 'role:tro_giang'])->group(function () {
    Route::get('/dashboard', [TroGiangDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lop-hoc', 'App\Http\Controllers\TroGiang\LopHocController@index')->name('tro-giang.lop-hoc.index');
    Route::get('/lop-hoc/{id}', 'App\Http\Controllers\TroGiang\LopHocController@show')->name('tro-giang.lop-hoc.show');
    Route::get('/cham-diem/tu-luan', 'App\Http\Controllers\TroGiang\ChamDiemController@tuLuanIndex')->name('tro-giang.cham-diem.tu-luan');
    Route::get('/cham-diem/file', 'App\Http\Controllers\TroGiang\ChamDiemController@fileIndex')->name('tro-giang.cham-diem.file');
    Route::post('/cham-diem/tu-luan/{id}', 'App\Http\Controllers\TroGiang\ChamDiemController@tuLuanUpdate')->name('tro-giang.cham-diem.tu-luan.update');
    Route::post('/cham-diem/file/{id}', 'App\Http\Controllers\TroGiang\ChamDiemController@fileUpdate')->name('tro-giang.cham-diem.file.update');
    Route::get('/hoc-vien', 'App\Http\Controllers\TroGiang\HocVienController@index')->name('tro-giang.hoc-vien.index');
    Route::get('/hoc-vien/{id}', 'App\Http\Controllers\TroGiang\HocVienController@show')->name('tro-giang.hoc-vien.show');
    
    // Bình luận
    Route::prefix('binh-luan')->name('binh-luan.')->group(function() {
        Route::get('/', [App\Http\Controllers\TroGiang\TroGiangController::class, 'danhSachBinhLuan'])->name('index');
        Route::post('/', [App\Http\Controllers\TroGiang\TroGiangController::class, 'luuBinhLuan'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\TroGiang\TroGiangController::class, 'xoaBinhLuan'])->name('destroy');
    });
    
    // Chấm bài
    Route::prefix('bai-tap')->name('bai-tap.')->group(function() {
        Route::get('/', [App\Http\Controllers\TroGiang\TroGiangController::class, 'danhSachBaiTap'])->name('danh-sach');
        Route::get('/cham-tu-luan/{id}', [App\Http\Controllers\TroGiang\TroGiangController::class, 'chamBaiTapTuLuan'])->name('cham-tu-luan');
        Route::post('/cham-tu-luan/{id}', [App\Http\Controllers\TroGiang\TroGiangController::class, 'luuDiemBaiTapTuLuan'])->name('luu-diem-tu-luan');
    });
    
    // Lương
    Route::get('/luong', [App\Http\Controllers\TroGiang\LuongController::class, 'index'])->name('luong.index');
    Route::get('/luong/{id}', [App\Http\Controllers\TroGiang\LuongController::class, 'show'])->name('luong.show');
    
    // Thông báo lớp học
    Route::resource('thong-bao', App\Http\Controllers\TroGiang\ThongBaoController::class);
    Route::get('/thong-bao/{id}/delete-file', [App\Http\Controllers\TroGiang\ThongBaoController::class, 'deleteFile'])->name('thong-bao.delete-file');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Admin\DashboardController@index')->name('dashboard');
    Route::resource('nguoi-dung', 'App\Http\Controllers\Admin\NguoiDungController');
    Route::resource('khoa-hoc', 'App\Http\Controllers\Admin\KhoaHocController');
    Route::resource('lop-hoc', 'App\Http\Controllers\Admin\LopHocController');
    Route::get('lop-hoc/export', 'App\Http\Controllers\Admin\LopHocController@export')->name('lop-hoc.export');
    Route::get('lop-hoc/{id}/danh-sach-hoc-vien', 'App\Http\Controllers\Admin\LopHocController@danhSachHocVien')->name('lop-hoc.danh-sach-hoc-vien');
    Route::get('lop-hoc/{id}/yeu-cau-tham-gia', 'App\Http\Controllers\Admin\LopHocController@danhSachYeuCauThamGia')->name('lop-hoc.yeu-cau-tham-gia');
    Route::post('lop-hoc/{id}/yeu-cau-tham-gia/{yeuCauId}/duyet', 'App\Http\Controllers\Admin\LopHocController@duyetYeuCauThamGia')->name('lop-hoc.duyet-yeu-cau');
    Route::post('lop-hoc/{id}/yeu-cau-tham-gia/{yeuCauId}/tu-choi', 'App\Http\Controllers\Admin\LopHocController@tuChoiYeuCauThamGia')->name('lop-hoc.tu-choi-yeu-cau');
    Route::post('lop-hoc/{id}/add-student', 'App\Http\Controllers\Admin\LopHocController@addStudent')->name('lop-hoc.add-student');
    Route::delete('lop-hoc/{id}/remove-student/{dangKyId}', 'App\Http\Controllers\Admin\LopHocController@removeStudent')->name('lop-hoc.remove-student');
    Route::put('dang-ky-hoc/{id}/xac-nhan', 'App\Http\Controllers\Admin\DangKyHocController@xacNhan')->name('dang-ky-hoc.xac-nhan');
    Route::resource('hoc-vien', 'App\Http\Controllers\Admin\HocVienController');
    Route::resource('giao-vien', 'App\Http\Controllers\Admin\GiaoVienController');
    Route::resource('tro-giang', 'App\Http\Controllers\Admin\TroGiangController');
    Route::resource('vai-tro', 'App\Http\Controllers\Admin\VaiTroController');
    Route::resource('quyen', 'App\Http\Controllers\Admin\QuyenController');
    Route::get('hoc-phi', 'App\Http\Controllers\Admin\HocPhiController@index')->name('hoc-phi.index');
    Route::get('thanh-toan', 'App\Http\Controllers\Admin\ThanhToanController@index')->name('thanh-toan.index');
    Route::get('luong', 'App\Http\Controllers\Admin\LuongController@index')->name('luong.index');
    Route::post('luong/tinh-toan', 'App\Http\Controllers\Admin\LuongController@calculate')->name('luong.calculate');
    Route::put('luong/{id}', 'App\Http\Controllers\Admin\LuongController@update')->name('luong.update');
    Route::get('thong-ke/tai-chinh', 'App\Http\Controllers\Admin\ThongKeController@taiChinh')->name('thong-ke.tai-chinh');
    Route::get('thong-ke/hoc-vien', 'App\Http\Controllers\Admin\ThongKeController@hocVien')->name('thong-ke.hoc-vien');
    Route::resource('thong-bao', 'App\Http\Controllers\Admin\ThongBaoController');
    Route::resource('tai-lieu', 'App\Http\Controllers\Admin\TaiLieuController');
    Route::resource('bai-hoc', 'App\Http\Controllers\Admin\BaiHocController');
    
    // Tài liệu bổ trợ
    Route::get('tai-lieu/download/{id}', [App\Http\Controllers\Admin\TaiLieuController::class, 'download'])->name('tai-lieu.download');
    
    // Quản lý thanh toán và học phí
    Route::prefix('thanh-toan')->name('thanh-toan.')->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\ThanhToanController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\ThanhToanController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [App\Http\Controllers\Admin\ThanhToanController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/cancel', [App\Http\Controllers\Admin\ThanhToanController::class, 'cancel'])->name('cancel');
        Route::get('/thong-ke/ngay', [App\Http\Controllers\Admin\ThanhToanController::class, 'thongKeTheoNgay'])->name('thong-ke-ngay');
        Route::get('/thong-ke/thang', [App\Http\Controllers\Admin\ThanhToanController::class, 'thongKeTheoThang'])->name('thong-ke-thang');
        Route::get('/export', [App\Http\Controllers\Admin\ThanhToanController::class, 'export'])->name('export');
    });
    
    // Thống kê
    Route::prefix('thong-ke')->name('thong-ke.')->group(function () {
        Route::get('/tong-quan', [App\Http\Controllers\Admin\ThongKeController::class, 'tongQuan'])->name('tong-quan');
        Route::get('/doanh-thu-ngay', [App\Http\Controllers\Admin\ThongKeController::class, 'doanhThuNgay'])->name('doanh-thu-ngay');
        Route::get('/doanh-thu-thang', [App\Http\Controllers\Admin\ThongKeController::class, 'doanhThuThang'])->name('doanh-thu-thang');
        Route::get('/chi-phi-luong', [App\Http\Controllers\Admin\ThongKeController::class, 'chiPhiLuong'])->name('chi-phi-luong');
        Route::get('/hoc-vien', [App\Http\Controllers\Admin\ThongKeController::class, 'hocVien'])->name('hoc-vien');
    });
    
    // Thông báo lớp học
    Route::resource('thong-bao', App\Http\Controllers\Admin\ThongBaoController::class);
    Route::get('/thong-bao/{id}/delete-file', [App\Http\Controllers\Admin\ThongBaoController::class, 'deleteFile'])->name('thong-bao.delete-file');
    Route::get('/thong-bao/{id}/change-status', [App\Http\Controllers\Admin\ThongBaoController::class, 'changeStatus'])->name('thong-bao.change-status');
    
    // Lương
    Route::get('/luong/{id}/thanh-toan', [App\Http\Controllers\Admin\LuongController::class, 'thanhToan'])->name('luong.thanh-toan');
    Route::get('/luong/{id}/huy', [App\Http\Controllers\Admin\LuongController::class, 'huy'])->name('luong.huy');
    Route::get('/luong-thong-ke', [App\Http\Controllers\Admin\LuongController::class, 'thongKe'])->name('luong.thong-ke');
});

// Routes cho quản lý lương
// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/luong', [App\Http\Controllers\Admin\LuongController::class, 'index'])->name('luong.index');
    // Route::get('/luong/{luong}', [App\Http\Controllers\Admin\LuongController::class, 'show'])->name('luong.show');
    Route::get('/luong/{luong}/edit', [App\Http\Controllers\Admin\LuongController::class, 'edit'])->name('luong.edit');
    // Route::put('/luong/{luong}', [App\Http\Controllers\Admin\LuongController::class, 'update'])->name('luong.update');
    // Route::put('/luong/{luong}/thanh-toan', [App\Http\Controllers\Admin\LuongController::class, 'thanhToan'])->name('luong.thanh-toan');
    Route::get('/lop-hoc/{lopHoc}/tao-luong', [App\Http\Controllers\Admin\LuongController::class, 'taoLuongKhiKetThucLop'])->name('luong.tao-luong');
});

// Giáo viên
Route::middleware(['auth', 'giao_vien'])->prefix('giao-vien')->name('giao-vien.')->group(function () {
    Route::get('/luong', [App\Http\Controllers\GiaoVien\LuongController::class, 'index'])->name('luong.index');
});

// Trợ giảng
Route::middleware(['auth', 'tro_giang'])->prefix('tro-giang')->name('tro-giang.')->group(function () {
    Route::get('/luong', [App\Http\Controllers\TroGiang\LuongController::class, 'index'])->name('luong.index');
});

// Routes cho thông báo
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('show');
});

// Route cho upload ảnh CKEditor
Route::post('/upload/image', [App\Http\Controllers\UploadController::class, 'uploadImage'])->name('upload.image');
