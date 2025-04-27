@extends('layouts.dashboard')

@section('title', 'Thống kê thanh toán theo tháng')
@section('page-heading', 'Thống kê thanh toán theo tháng')

@php
    $active = 'nguoi-dung';
    $role = 'admin';
@endphp
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Thống kê thanh toán theo tháng</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.thanh-toan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Chọn tháng thống kê</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.thanh-toan.thong-ke-thang') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="thang" class="block text-gray-700 mb-2">Tháng:</label>
                    <input type="month" id="thang" name="thang" value="{{ $thang }}" 
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
                <span class="text-4xl font-bold text-blue-600">{{ number_format($tongThanhToanThang) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-green-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-money-bill-wave mr-2"></i>Tổng số tiền
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-green-600">{{ number_format($tongTienThang, 0, ',', '.') }} đ</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-purple-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-purple-800">
                    <i class="fas fa-calendar-alt mr-2"></i>Tháng
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-2xl font-bold text-purple-600">{{ \Carbon\Carbon::createFromFormat('Y-m', $thang)->format('m/Y') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                <h2 class="text-lg font-semibold">Thống kê theo trạng thái thanh toán</h2>
            </div>
            <div class="p-4">
                @if($thongKeTrangThai->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trạng thái
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
                                @foreach($thongKeTrangThai as $trangThai)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($trangThai['ten'] == 'Đã thanh toán' || $trangThai['ten'] == 'Đã xác nhận') 
                                                    bg-green-100 text-green-800
                                                @elseif($trangThai['ten'] == 'Chờ xác nhận') 
                                                    bg-yellow-100 text-yellow-800
                                                @elseif($trangThai['ten'] == 'Đã hủy') 
                                                    bg-red-100 text-red-800
                                                @else 
                                                    bg-gray-100 text-gray-800 
                                                @endif">
                                                {{ $trangThai['ten'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($trangThai['so_luong']) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($trangThai['tong_tien'], 0, ',', '.') }} đ</div>
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Thống kê thanh toán theo ngày trong tháng</h2>
        </div>
        <div class="p-4">
            @if($thongKeTheoNgay->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số lượng thanh toán
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tổng tiền
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($thongKeTheoNgay as $ngay)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $ngay['ngay'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ number_format($ngay['so_luong']) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($ngay['tong_tien'], 0, ',', '.') }} đ</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($ngay['so_luong'] > 0)
                                            <a href="{{ route('admin.thanh-toan.thong-ke-ngay', ['ngay' => $ngay['ngay_raw']]) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-eye mr-1"></i>Xem chi tiết
                                            </a>
                                        @else
                                            <span class="text-gray-400">Không có dữ liệu</span>
                                        @endif
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