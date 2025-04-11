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
Route::get('/register-interest', [AuthController::class, 'showRegisterInterestForm'])->name('register.interest');
Route::post('/register-interest', [AuthController::class, 'registerInterest']);

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
    Route::get('/lop-hoc/{lopId}/bai-hoc/{baiHocId}', [HocVienBaiHocController::class, 'show'])->name('bai-hoc.show');
    Route::post('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/cap-nhat-tien-do', [HocVienBaiHocController::class, 'capNhatTienDo'])->name('bai-hoc.cap-nhat-tien-do');
    Route::get('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/bai-tap/{baiTapId}/nop-bai', [HocVienBaiHocController::class, 'formNopBaiTap'])->name('bai-hoc.form-nop-bai-tap');
    Route::post('/lop-hoc/{lopHocId}/bai-hoc/{baiHocId}/bai-tap/{baiTapId}/nop-bai', [HocVienBaiHocController::class, 'nopBaiTap'])->name('bai-hoc.nop-bai-tap');
    
    // Bài tập
    Route::prefix('bai-tap')->name('bai-tap.')->group(function () {
        Route::get('/{id}', [App\Http\Controllers\HocVien\BaiTapController::class, 'show'])->name('show');
        Route::get('/{id}/lam-bai', [App\Http\Controllers\HocVien\BaiTapController::class, 'lamBai'])->name('lam-bai');
        Route::get('/{id}/lam-bai-trac-nghiem', [App\Http\Controllers\HocVien\BaiTapController::class, 'lamBaiTracNghiem'])->name('lam-bai-trac-nghiem');
        Route::post('/{id}/nop-bai-trac-nghiem', [App\Http\Controllers\HocVien\BaiTapController::class, 'nopBaiTracNghiem'])->name('nop-bai-trac-nghiem');
        Route::get('/{id}/form-nop-bai', [App\Http\Controllers\HocVien\BaiTapController::class, 'formNopBai'])->name('form-nop-bai');
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
    Route::get('/cham-diem/tu-luan/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@tuLuan')->name('cham-diem.tu-luan');
    Route::post('/cham-diem/tu-luan/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@chamTuLuan')->name('cham-diem.cham-tu-luan');
    Route::get('/cham-diem/trac-nghiem/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@tracNghiem')->name('cham-diem.trac-nghiem');
    Route::post('/cham-diem/trac-nghiem/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@chamTracNghiem')->name('cham-diem.cham-trac-nghiem');
    Route::post('/cham-diem/cap-nhat-trang-thai/{id}', 'App\Http\Controllers\GiaoVien\ChamDiemController@capNhatTrangThai')->name('cham-diem.cap-nhat-trang-thai');
    
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
});
