@extends('layouts.dashboard')

@section('title', 'Chi tiết lớp học: ' . $lopHoc->ten)
@section('page-heading', 'Chi tiết lớp học: ' . $lopHoc->ten)

@php
    $active = 'lop-hoc';
    $role = 'tro_giang';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Thông tin chung -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-800">Thông tin lớp học</h2>
            <div>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    @if($lopHoc->trang_thai == 'dang_dien_ra') bg-green-100 text-green-800
                    @elseif($lopHoc->trang_thai == 'sap_khai_giang') bg-yellow-100 text-yellow-800
                    @elseif($lopHoc->trang_thai == 'da_ket_thuc') bg-gray-100 text-gray-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    @if($lopHoc->trang_thai == 'dang_dien_ra')
                        Đang diễn ra
                    @elseif($lopHoc->trang_thai == 'sap_khai_giang')
                        Sắp khai giảng
                    @elseif($lopHoc->trang_thai == 'da_ket_thuc')
                        Đã kết thúc
                    @else
                        {{ ucfirst(str_replace('_', ' ', $lopHoc->trang_thai)) }}
                    @endif
                </span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500">Thông tin cơ bản</h3>
                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="text-xs text-gray-500">Tên lớp</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ten }}</div>
                        </div>
                        @if($lopHoc->ma_lop)
                        <div>
                            <div class="text-xs text-gray-500">Mã lớp</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ma_lop }}</div>
                        </div>
                        @endif
                        <div>
                            <div class="text-xs text-gray-500">Thuộc khóa học</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->khoaHoc->ten }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500">Thời gian</h3>
                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="text-xs text-gray-500">Ngày bắt đầu</div>
                            <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</div>
                        </div>
                        @if($lopHoc->ngay_ket_thuc)
                        <div>
                            <div class="text-xs text-gray-500">Ngày kết thúc</div>
                            <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</div>
                        </div>
                        @endif
                        <div>
                            <div class="text-xs text-gray-500">Thời lượng</div>
                            <div class="text-sm font-medium text-gray-900">
                                @if($lopHoc->ngay_ket_thuc)
                                    {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->diffInDays(\Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)) + 1 }} ngày
                                @else
                                    Chưa xác định
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500">Tổng quan</h3>
                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="text-xs text-gray-500">Tổng số học viên</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->dang_ky_hocs_count ?? 0 }} học viên</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Tổng số bài học</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->bai_hoc_lops_count ?? 0 }} bài học</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Tổng số bài tập</div>
                            <div class="text-sm font-medium text-gray-900">{{ $lopHoc->bai_taps_count ?? 0 }} bài tập</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>
@endsection 