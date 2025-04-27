@extends('layouts.dashboard')

@section('title', 'Thống kê thanh toán theo ngày')
@section('page-heading', 'Thống kê thanh toán theo ngày')

@php
    $active = 'nguoi-dung';
    $role = 'admin';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Thống kê thanh toán theo ngày</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.thanh-toan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Chọn ngày thống kê</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.thanh-toan.thong-ke-ngay') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="ngay" class="block text-gray-700 mb-2">Ngày:</label>
                    <input type="date" id="ngay" name="ngay" value="{{ $ngay }}" 
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Xem thống kê
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-blue-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-blue-800">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Tổng số thanh toán
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-blue-600">{{ number_format($tongThanhToanNgay) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-green-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-money-bill-wave mr-2"></i>Tổng số tiền
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-green-600">{{ number_format($tongTienNgay, 0, ',', '.') }} đ</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-purple-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-purple-800">
                    <i class="fas fa-calendar-day mr-2"></i>Ngày
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-2xl font-bold text-purple-600">{{ Carbon\Carbon::parse($ngay)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Thống kê theo phương thức thanh toán</h2>
        </div>
        <div class="p-4">
            @if($thongKePhuongThuc->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phương thức
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số lượng
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tổng tiền
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($thongKePhuongThuc as $phuongThuc)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $phuongThuc['ten'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ number_format($phuongThuc['so_luong']) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($phuongThuc['tong_tien'], 0, ',', '.') }} đ</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Không có dữ liệu thanh toán</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Danh sách thanh toán trong ngày</h2>
        </div>
        <div class="p-4">
            @if($thanhToansNgay->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mã thanh toán
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Học viên
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lớp học
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số tiền
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phương thức
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thời gian
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($thanhToansNgay as $thanhToan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $thanhToan->ma_thanh_toan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->hocVien && $thanhToan->dangKyHoc->hocVien->nguoiDung)
                                            <div class="text-sm font-medium text-gray-900">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten }}</div>
                                            <div class="text-sm text-gray-500">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->email }}</div>
                                        @else
                                            <div class="text-sm text-gray-500">Không có dữ liệu</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->lopHoc)
                                            <div class="text-sm font-medium text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</div>
                                            @if($thanhToan->dangKyHoc->lopHoc->khoaHoc)
                                                <div class="text-sm text-gray-500">{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</div>
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-500">Không có dữ liệu</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} đ</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($thanhToan->phuong_thuc == 'chuyen_khoan') bg-blue-100 text-blue-800
                                            @elseif($thanhToan->phuong_thuc == 'tien_mat') bg-green-100 text-green-800
                                            @elseif($thanhToan->phuong_thuc == 'vnpay') bg-yellow-100 text-yellow-800
                                            @elseif($thanhToan->phuong_thuc == 'vi_dien_tu') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($thanhToan->phuong_thuc == 'chuyen_khoan')
                                                Chuyển khoản ngân hàng
                                            @elseif($thanhToan->phuong_thuc == 'tien_mat')
                                                Tiền mặt
                                            @elseif($thanhToan->phuong_thuc == 'vnpay')
                                                VNPay
                                            @elseif($thanhToan->phuong_thuc == 'vi_dien_tu')
                                                Ví điện tử
                                            @else
                                                {{ $thanhToan->phuong_thuc }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($thanhToan->trang_thai == 'da_thanh_toan' || $thanhToan->trang_thai == 'da_xac_nhan') bg-green-100 text-green-800
                                            @elseif($thanhToan->trang_thai == 'cho_xac_nhan') bg-yellow-100 text-yellow-800
                                            @elseif($thanhToan->trang_thai == 'da_huy') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($thanhToan->trang_thai == 'da_thanh_toan')
                                                Đã thanh toán
                                            @elseif($thanhToan->trang_thai == 'da_xac_nhan')
                                                Đã xác nhận
                                            @elseif($thanhToan->trang_thai == 'cho_xac_nhan')
                                                Chờ xác nhận
                                            @elseif($thanhToan->trang_thai == 'da_huy')
                                                Đã hủy
                                            @else
                                                {{ $thanhToan->trang_thai }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ $thanhToan->created_at->format('H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('admin.thanh-toan.show', $thanhToan->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Không có dữ liệu thanh toán</p>
            @endif
        </div>
    </div>
</div>
@endsection 