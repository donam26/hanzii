@extends('layouts.dashboard')

@section('title', 'Quản lý thanh toán lương')
@section('page-heading', 'Quản lý thanh toán lương')

@php
    $active = 'thanh-toan-luong';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white shadow-md rounded-lg p-4 border-l-4 border-red-500">
            <h6 class="text-sm font-medium text-gray-500">Tổng lớp đã kết thúc</h6>
            <p class="text-2xl font-semibold mt-2">{{ $tongLopDaKetThuc }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 border-l-4 border-green-500">
            <h6 class="text-sm font-medium text-gray-500">Lớp đã trả lương</h6>
            <p class="text-2xl font-semibold mt-2">{{ $tongLopDaTraLuong }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 border-l-4 border-yellow-500">
            <h6 class="text-sm font-medium text-gray-500">Lớp chưa trả lương</h6>
            <p class="text-2xl font-semibold mt-2">{{ $tongLopChuaTraLuong }}</p>
        </div>
    </div>

    <!-- Bảng danh sách lớp -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <h5 class="text-lg font-medium text-gray-900">Danh sách lớp đã kết thúc</h5>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã lớp</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên lớp</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giáo viên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trợ giảng</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lopHocs as $lopHoc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ma_lop }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ten }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lopHoc->thanhToanLuong && $lopHoc->thanhToanLuong->daThanhToanDayDu())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Đã trả lương
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Chưa trả lương
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $lopHoc->giaoVien->nguoiDung->ho_ten ?? 'Chưa có' }}
                                @if($lopHoc->thanhToanLuong && $lopHoc->thanhToanLuong->trang_thai_giao_vien === 'da_thanh_toan')
                                    <span class="ml-1 text-green-500">
                                        <i class="fas fa-check-circle" title="Đã trả lương"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $lopHoc->troGiang->nguoiDung->ho_ten ?? 'Chưa có' }}
                                @if($lopHoc->thanhToanLuong && $lopHoc->thanhToanLuong->trang_thai_tro_giang === 'da_thanh_toan')
                                    <span class="ml-1 text-green-500">
                                        <i class="fas fa-check-circle" title="Đã trả lương"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.thanh-toan-luong.show', $lopHoc->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-eye mr-1.5"></i> Chi tiết trả lương
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Không có lớp học nào đã kết thúc
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 