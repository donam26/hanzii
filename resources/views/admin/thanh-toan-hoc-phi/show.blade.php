@extends('layouts.dashboard')

@section('title', 'Chi tiết thanh toán học phí')
@section('page-heading', 'Chi tiết thanh toán học phí')

@php
    $active = 'thanh-toan-hoc-phi';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Hiển thị thông báo -->
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Thành công!</strong>
        <span class="block sm:inline ml-1">{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Lỗi!</strong>
        <span class="block sm:inline ml-1">{{ session('error') }}</span>
    </div>
    @endif

    @if (session('mat_khau_moi'))
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Lưu ý!</strong>
        <span class="block sm:inline ml-1">
            Tài khoản học viên đã được tạo với mật khẩu mới: <span class="font-bold">{{ session('mat_khau_moi') }}</span> 
            cho email: <span class="font-bold">{{ session('email_hoc_vien') }}</span>
        </span>
        <p class="mt-2">Vui lòng thông báo cho học viên thông tin đăng nhập này.</p>
    </div>
    @endif

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết thanh toán học phí #{{ $thanhToanHocPhi->id }}</h2>
        <a href="{{ route('admin.thanh-toan-hoc-phi.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Thông tin thanh toán
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Chi tiết thanh toán học phí của học viên
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Mã thanh toán
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thanhToanHocPhi->ma_thanh_toan }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Học viên
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($thanhToanHocPhi->hocVien && $thanhToanHocPhi->hocVien->nguoiDung)
                            {{ $thanhToanHocPhi->hocVien->nguoiDung->ho }} {{ $thanhToanHocPhi->hocVien->nguoiDung->ten }}
                            <div class="text-xs text-gray-500 mt-1">
                                Email: {{ $thanhToanHocPhi->hocVien->nguoiDung->email }}<br>
                                SĐT: {{ $thanhToanHocPhi->hocVien->nguoiDung->so_dien_thoai }}
                            </div>
                        @else
                            <span class="text-red-500">Không có thông tin học viên</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Lớp học
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($thanhToanHocPhi->lopHoc)
                            <div>{{ $thanhToanHocPhi->lopHoc->ten }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Mã lớp: {{ $thanhToanHocPhi->lopHoc->ma_lop }}<br>
                                Thời gian: {{ $thanhToanHocPhi->lopHoc->ngay_bat_dau ? \Carbon\Carbon::parse($thanhToanHocPhi->lopHoc->ngay_bat_dau)->format('d/m/Y') : 'N/A' }} - 
                                {{ $thanhToanHocPhi->lopHoc->ngay_ket_thuc ? \Carbon\Carbon::parse($thanhToanHocPhi->lopHoc->ngay_ket_thuc)->format('d/m/Y') : 'N/A' }}
                            </div>
                        @else
                            <span class="text-red-500">Không có thông tin lớp học</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Số tiền
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="text-lg font-bold text-gray-900">{{ number_format($thanhToanHocPhi->so_tien, 0, ',', '.') }} VNĐ</span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Trạng thái
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($thanhToanHocPhi->trang_thai == 'da_thanh_toan')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Đã thanh toán
                            </span>
                            @if($thanhToanHocPhi->ngay_thanh_toan)
                                <div class="text-xs text-gray-500 mt-1">
                                    Ngày thanh toán: {{ \Carbon\Carbon::parse($thanhToanHocPhi->ngay_thanh_toan)->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Chưa thanh toán
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Thời gian
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-gray-500">Ngày tạo:</span>
                                <div>{{ \Carbon\Carbon::parse($thanhToanHocPhi->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Ngày cập nhật:</span>
                                <div>{{ \Carbon\Carbon::parse($thanhToanHocPhi->updated_at)->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Ghi chú
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thanhToanHocPhi->ghi_chu ?? 'Không có ghi chú' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Phần kiểm tra trạng thái học viên trong lớp học -->
    @if($thanhToanHocPhi->hocVien && $thanhToanHocPhi->lopHoc)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Trạng thái trong lớp học
                </h3>
            </div>
            <div class="border-t border-gray-200 p-4">
                @php
                    $dangKyHoc = \App\Models\DangKyHoc::where('hoc_vien_id', $thanhToanHocPhi->hocVien->id)
                        ->where('lop_hoc_id', $thanhToanHocPhi->lopHoc->id)
                        ->first();
                @endphp
                
                @if($dangKyHoc)
                    <div class="flex items-center bg-blue-50 p-3 rounded-md">
                        <i class="fas fa-user-check text-blue-500 mr-2"></i>
                        <div>
                            <p class="text-blue-800">Học viên đã được thêm vào lớp học</p>
                            <p class="text-xs text-blue-600">Trạng thái: 
                                @if($dangKyHoc->trang_thai == 'da_xac_nhan')
                                    <span class="font-semibold">Đã xác nhận</span>
                              
                                @elseif($dangKyHoc->trang_thai == 'da_hoan_thanh')
                                    <span class="font-semibold">Đã hoàn thành</span>
                                @else
                                    <span class="font-semibold">{{ $dangKyHoc->trang_thai }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-blue-600">Ngày tham gia: {{ $dangKyHoc->ngay_tham_gia ? \Carbon\Carbon::parse($dangKyHoc->ngay_tham_gia)->format('d/m/Y') : 'Chưa xác định' }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center bg-yellow-50 p-3 rounded-md">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        <p class="text-yellow-800">Học viên chưa được thêm vào lớp học</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        @if($thanhToanHocPhi->trang_thai != 'da_thanh_toan')
            <form action="{{ route('admin.thanh-toan-hoc-phi.update-status', $thanhToanHocPhi->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-check-circle mr-2"></i> Xác nhận đã thanh toán
                </button>
            </form>
        @else
            <form action="{{ route('admin.thanh-toan-hoc-phi.cancel-status', $thanhToanHocPhi->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-undo mr-2"></i> Đánh dấu chưa thanh toán
                </button>
            </form>
        @endif
        <a href="{{ route('admin.thanh-toan-hoc-phi.edit', $thanhToanHocPhi->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-edit mr-2"></i> Chỉnh sửa
        </a>
    </div>
</div>
@endsection 