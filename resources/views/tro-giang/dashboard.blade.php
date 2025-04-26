@extends('layouts.dashboard')

@section('title', 'Bảng điều khiển Trợ giảng')
@section('page-heading', 'Bảng điều khiển Trợ giảng')

@php
    $active = 'dashboard';
    $role = 'tro_giang';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tổng số lớp học -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="rounded-full bg-red-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-600 font-medium">Lớp học quản lý</h2>
                <p class="text-2xl font-semibold text-gray-800">{{ $soLuongLopHoc }}</p>
            </div>
        </div>

        <!-- Tổng số học viên -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-600 font-medium">Học viên quản lý</h2>
                <p class="text-2xl font-semibold text-gray-800">{{ $soLuongHocVien }}</p>
            </div>
        </div>

        <!-- Bài tập cần chấm -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="rounded-full bg-yellow-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-600 font-medium">Bài tập cần chấm</h2>
                <p class="text-2xl font-semibold text-gray-800">{{ $soLuongBaiTapCanCham }}</p>
            </div>
        </div>

        <!-- Bình luận mới -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="rounded-full bg-green-100 p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm text-gray-600 font-medium">Bình luận mới (7 ngày)</h2>
                <p class="text-2xl font-semibold text-gray-800">{{ $soLuongBinhLuanMoi }}</p>
            </div>
        </div>
    </div>
    
    <!-- Các lớp học đang phụ trách -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-800">Lớp học đang phụ trách</h2>
            <a href="{{ route('tro-giang.lop-hoc.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Xem tất cả</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên lớp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc khóa học</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Học viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lopHocs as $lopHoc)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ten }}</div>
                                <div class="text-sm text-gray-500">Bắt đầu: {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $lopHoc->khoaHoc->ten }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $lopHoc->dang_ky_hocs_count ?? 0 }} học viên</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($lopHoc->trang_thai == 'dang_dien_ra') bg-green-100 text-green-800
                                    @elseif($lopHoc->trang_thai == 'sap_dien_ra') bg-yellow-100 text-yellow-800
                                    @elseif($lopHoc->trang_thai == 'da_ket_thuc') bg-gray-100 text-gray-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    @if($lopHoc->trang_thai == 'dang_dien_ra')
                                        Đang diễn ra
                                    @elseif($lopHoc->trang_thai == 'sap_dien_ra')
                                        Sắp diễn ra
                                    @elseif($lopHoc->trang_thai == 'da_ket_thuc')
                                        Đã kết thúc
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $lopHoc->trang_thai)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tro-giang.lop-hoc.show', $lopHoc->id) }}" class="text-red-600 hover:text-red-900">Chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Bạn chưa được phân công lớp học nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Link nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quản lý bài tập -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-800">Quản lý bài tập</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <p class="text-sm text-gray-600 mb-4">Quản lý bài tập của học viên, chấm điểm và cung cấp phản hồi</p>
            <a href="{{ route('tro-giang.bai-tap.index') }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                Quản lý bài tập
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        
        <!-- Quản lý học viên -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-800">Học viên</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <p class="text-sm text-gray-600 mb-4">Xem tiến độ học tập và quản lý thông tin học viên trong các lớp</p>
            <a href="{{ route('tro-giang.hoc-vien.index') }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                Quản lý học viên
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        
        <!-- Bình luận -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-800">Bình luận</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <p class="text-sm text-gray-600 mb-4">Xem và trả lời các bình luận từ học viên trong các bài học</p>
            <a href="{{ route('tro-giang.lop-hoc.index') }}" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800">
                Xem bình luận
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection 