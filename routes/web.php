<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HocVien\DashboardController as HocVienDashboardController;
use App\Http\Controllers\HocVien\LopHocController as HocVienLopHocController;
use App\Http\Controllers\HocVien\BaiHocController as HocVienBaiHocController;
use App\Http\Controllers\HocVien\BaiTapController as HocVienBaiTapController;
use App\Http\Controllers\HocVien\KetQuaController as HocVienKetQuaController;
use App\Http\Controllers\GiaoVien\DashboardController as GiaoVienDashboardController;
use App\Http\Controllers\TroGiang\DashboardController as TroGiangDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LienHeController;
use App\Http\Controllers\Admin\ThanhToanHocPhiController;

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

// Route cho form liên hệ
Route::get('/lien-he', [LienHeController::class, 'index'])->name('lien-he');
Route::post('/lien-he', [LienHeController::class, 'store'])->name('lien-he.store');

// Route thử nghiệm thông báo (chỉ để test)
Route::get('/test-thong-bao', [LienHeController::class, 'testNotification'])->name('test-thong-bao');

// Route cho danh sách khóa học và chi tiết khóa học (public)
Route::get('/khoa-hoc-all', [HomeController::class, 'allCourses'])->name('all-courses');
Route::get('/khoa-hoc/{id}', [HomeController::class, 'showCourse'])->name('course.show');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Học viên routes
Route::prefix('hoc-vien')->name('hoc-vien.')->middleware(['auth', 'role:hoc_vien'])->group(function () {
    Route::get('/dashboard', [HocVienDashboardController::class, 'index'])->name('dashboard');
    
    // Lớp học
    Route::prefix('lop-hoc')->name('lop-hoc.')->group(function() {
        Route::get('/', [App\Http\Controllers\HocVien\LopHocController::class, 'index'])->name('index');
        Route::get('/{id}/progress', [App\Http\Controllers\HocVien\LopHocController::class, 'progress'])->name('progress');
        Route::get('/{id}/danh-sach-hoc-vien', [App\Http\Controllers\HocVien\LopHocController::class, 'danhSachHocVien'])->name('danh-sach-hoc-vien');
        Route::get('/tim-kiem', [App\Http\Controllers\HocVien\LopHocController::class, 'formTimKiem'])->name('form-tim-kiem');
        Route::post('/tim-kiem', [App\Http\Controllers\HocVien\LopHocController::class, 'timKiem'])->name('tim-kiem');
        Route::post('/tim-lop', [App\Http\Controllers\HocVien\LopHocController::class, 'timLop'])->name('tim-lop');
        Route::post('/tham-gia/{id}', [App\Http\Controllers\HocVien\LopHocController::class, 'thamGia'])->name('tham-gia');
        Route::post('/gui-yeu-cau', [App\Http\Controllers\HocVien\LopHocController::class, 'guiYeuCau'])->name('gui-yeu-cau');
        Route::get('/yeu-cau', [App\Http\Controllers\HocVien\LopHocController::class, 'danhSachYeuCau'])->name('yeu-cau');
        Route::get('/{id}', [App\Http\Controllers\HocVien\LopHocController::class, 'show'])->name('show');
        Route::post('/complete-bai-hoc', [App\Http\Controllers\HocVien\LopHocController::class, 'completeBaiHoc'])->name('complete-bai-hoc');
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
    Route::get('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/xem-tai-lieu/{taiLieuId}', [HocVienBaiHocController::class, 'xemTaiLieu'])->name('bai-hoc.xem-tai-lieu');
    
    // Kết quả học tập
    Route::get('/ket-qua-hoc-tap', [HocVienKetQuaController::class, 'index'])->name('ket-qua.index');
    Route::get('/ket-qua', [HocVienKetQuaController::class, 'index'])->name('ket-qua');
    Route::get('/ket-qua/{id}', [HocVienKetQuaController::class, 'show'])->name('ket-qua.show');
    Route::get('/ket-qua/download/{id}', [HocVienKetQuaController::class, 'download'])->name('ket-qua.download');
    
    // Thông tin cá nhân
    Route::get('/profile', [App\Http\Controllers\HocVien\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\HocVien\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\HocVien\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [App\Http\Controllers\HocVien\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [App\Http\Controllers\HocVien\ProfileController::class, 'changePassword'])->name('profile.update-password');
    
    // Bình luận
    Route::post('/binh-luan', [App\Http\Controllers\HocVien\BinhLuanController::class, 'store'])->name('binh-luan.store');
    Route::delete('/binh-luan/{id}', [App\Http\Controllers\HocVien\BinhLuanController::class, 'destroy'])->name('binh-luan.destroy');
    
   
    
    // Thông báo lớp học
    Route::get('/thong-bao', [App\Http\Controllers\HocVien\ThongBaoController::class, 'index'])->name('thong-bao.index');
    Route::get('/thong-bao/{id}', [App\Http\Controllers\HocVien\ThongBaoController::class, 'show'])->name('thong-bao.show');
    Route::post('/thong-bao/mark-all-as-read', [App\Http\Controllers\HocVien\ThongBaoController::class, 'markAllAsRead'])->name('thong-bao.mark-all-as-read');
    Route::get('/thong-bao/count-unread', [App\Http\Controllers\HocVien\ThongBaoController::class, 'countUnread'])->name('thong-bao.count-unread');
   
});

// Giáo viên routes
Route::prefix('giao-vien')->name('giao-vien.')->middleware(['auth', 'role:giao_vien'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\GiaoVien\DashboardController@index')->name('dashboard');
    
    // Thông tin cá nhân
    Route::get('/profile', [App\Http\Controllers\GiaoVien\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\GiaoVien\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\GiaoVien\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [App\Http\Controllers\GiaoVien\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [App\Http\Controllers\GiaoVien\ProfileController::class, 'changePassword'])->name('profile.update-password');
    
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
    
    // Quản lý tài liệu bổ trợ
    Route::get('/tai-lieu/download/{id}', 'App\Http\Controllers\GiaoVien\TaiLieuController@download')->name('tai-lieu.download');
    Route::delete('/tai-lieu/{id}', 'App\Http\Controllers\GiaoVien\TaiLieuController@destroy')->name('tai-lieu.destroy');
    
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
    Route::get('/lop-hoc', 'App\Http\Controllers\TroGiang\LopHocController@index')->name('lop-hoc.index');
    Route::get('/lop-hoc/{id}', 'App\Http\Controllers\TroGiang\LopHocController@show')->name('lop-hoc.show');
    Route::get('/lop-hoc/{id}/danh-sach-hoc-vien', 'App\Http\Controllers\TroGiang\LopHocController@danhSachHocVien')->name('lop-hoc.danh-sach-hoc-vien');
    Route::get('/lop-hoc/{id}/danh-sach-bai-tap', 'App\Http\Controllers\TroGiang\LopHocController@danhSachBaiTap')->name('lop-hoc.danh-sach-bai-tap');
    
    // Thông tin cá nhân
    Route::get('/profile', 'App\Http\Controllers\TroGiang\ProfileController@index')->name('profile.index');
    Route::get('/profile/edit', 'App\Http\Controllers\TroGiang\ProfileController@edit')->name('profile.edit');
    Route::put('/profile', 'App\Http\Controllers\TroGiang\ProfileController@update')->name('profile.update');
    Route::get('/change-password', 'App\Http\Controllers\TroGiang\ProfileController@showChangePasswordForm')->name('profile.change-password');
    Route::post('/change-password', 'App\Http\Controllers\TroGiang\ProfileController@changePassword')->name('profile.update-password');
    
    // Bài học
    Route::get('/bai-hoc/{lopHocId}/{baiHocId}', 'App\Http\Controllers\TroGiang\BaiHocController@show')->name('bai-hoc.show');
    
    // Bình luận
    Route::prefix('binh-luan')->name('binh-luan.')->group(function() {
        Route::get('/', [App\Http\Controllers\TroGiang\BinhLuanController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\TroGiang\BinhLuanController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\TroGiang\BinhLuanController::class, 'destroy'])->name('destroy');
        Route::post('/phan-hoi', [App\Http\Controllers\TroGiang\BinhLuanController::class, 'phanHoi'])->name('phan-hoi');
    });
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', 'App\Http\Controllers\Admin\DashboardController@index')->name('dashboard');
    
    // Thông tin cá nhân
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.update-password');
    
    // Quản lý thanh toán học phí
    Route::resource('thanh-toan-hoc-phi', ThanhToanHocPhiController::class);
    Route::post('/thanh-toan-hoc-phi/{thanhToanHocPhi}/update-status', [ThanhToanHocPhiController::class, 'updateStatus'])->name('thanh-toan-hoc-phi.update-status');
    Route::post('/thanh-toan-hoc-phi/{thanhToanHocPhi}/cancel-status', [ThanhToanHocPhiController::class, 'cancelStatus'])->name('thanh-toan-hoc-phi.cancel-status');
    
    // Quản lý lương giáo viên và trợ giảng
    Route::get('luong', 'App\Http\Controllers\Admin\LuongController@index')->name('luong.index');
    Route::post('luong/tinh-toan', 'App\Http\Controllers\Admin\LuongController@calculate')->name('luong.calculate');
    Route::get('luong/thong-ke', 'App\Http\Controllers\Admin\LuongController@thongKe')->name('luong.thong-ke');
    Route::get('luong/giao-vien/{luongGiaoVien}', 'App\Http\Controllers\Admin\LuongController@showGiaoVien')->name('luong.show-giao-vien');
    Route::get('luong/tro-giang/{luongTroGiang}', 'App\Http\Controllers\Admin\LuongController@showTroGiang')->name('luong.show-tro-giang');
    Route::post('luong/giao-vien/{luongGiaoVien}/thanh-toan', 'App\Http\Controllers\Admin\LuongController@thanhToanGiaoVien')->name('luong.thanh-toan-giao-vien');
    Route::post('luong/tro-giang/{luongTroGiang}/thanh-toan', 'App\Http\Controllers\Admin\LuongController@thanhToanTroGiang')->name('luong.thanh-toan-tro-giang');
    Route::post('luong/giao-vien/{luongGiaoVien}/huy', 'App\Http\Controllers\Admin\LuongController@huyThanhToanGiaoVien')->name('luong.huy-giao-vien');
    Route::post('luong/tro-giang/{luongTroGiang}/huy', 'App\Http\Controllers\Admin\LuongController@huyThanhToanTroGiang')->name('luong.huy-tro-giang');
    
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
    // Đăng ký học
    Route::put('dang-ky-hoc/{id}/xac-nhan', 'App\Http\Controllers\Admin\DangKyHocController@xacNhan')->name('dang-ky-hoc.xac-nhan');
    Route::put('dang-ky-hoc/{id}/tu-choi', 'App\Http\Controllers\Admin\DangKyHocController@tuChoi')->name('dang-ky-hoc.tu-choi');
    
    Route::resource('hoc-vien', 'App\Http\Controllers\Admin\HocVienController');
    Route::resource('giao-vien', 'App\Http\Controllers\Admin\GiaoVienController');
    Route::resource('tro-giang', 'App\Http\Controllers\Admin\TroGiangController');
    // Route::resource('vai-tro', 'App\Http\Controllers\Admin\VaiTroController');
    // Route::resource('quyen', 'App\Http\Controllers\Admin\QuyenController');
    Route::get('hoc-phi', 'App\Http\Controllers\Admin\HocPhiController@index')->name('hoc-phi.index');
    Route::get('thong-ke/tai-chinh', 'App\Http\Controllers\Admin\ThongKeController@taiChinh')->name('thong-ke.tai-chinh');
    Route::get('thong-ke/hoc-vien', 'App\Http\Controllers\Admin\ThongKeController@hocVien')->name('thong-ke.hoc-vien');
    Route::resource('thong-bao', 'App\Http\Controllers\Admin\ThongBaoController');
    Route::resource('tai-lieu', 'App\Http\Controllers\Admin\TaiLieuController');
    Route::resource('bai-hoc', 'App\Http\Controllers\Admin\BaiHocController');
    Route::delete('bai-hoc/xoa-tai-lieu/{id}', [App\Http\Controllers\Admin\BaiHocController::class, 'xoaTaiLieu'])->name('bai-hoc.xoa-tai-lieu');
    
    // Tài liệu bổ trợ
    Route::get('tai-lieu/download/{id}', [App\Http\Controllers\Admin\TaiLieuController::class, 'download'])->name('tai-lieu.download');
   
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
    
    // Quản lý liên hệ
    Route::prefix('lien-he')->name('lien-he.')->group(function () {
        Route::get('/', [LienHeController::class, 'danhSach'])->name('index');
        Route::get('/{id}', [LienHeController::class, 'show'])->name('show');
        Route::put('/{id}/cap-nhat-trang-thai', [LienHeController::class, 'capNhatTrangThai'])->name('cap-nhat-trang-thai');
        Route::post('/{id}/send-response', [LienHeController::class, 'sendResponse'])->name('send-response');
    });
});


// Routes cho thông báo
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('show');
});

// Route cho upload ảnh CKEditor
Route::post('/upload/image', [App\Http\Controllers\UploadController::class, 'uploadImage'])->name('upload.image');
Route::post('/ckeditor/upload', [App\Http\Controllers\UploadController::class, 'ckeditorUpload'])->name('ckeditor.upload');
