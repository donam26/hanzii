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
    
    <!-- Các hành động chính -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('tro-giang.lop-hoc.danh-sach-hoc-vien', $lopHoc->id) }}" class="bg-white rounded-lg shadow overflow-hidden flex flex-col hover:shadow-lg transition duration-150">
            <div class="p-6 flex items-center justify-between border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Danh sách học viên</h3>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $lopHoc->dang_ky_hocs_count ?? 0 }}</span>
            </div>
            <div class="p-6 text-gray-600">
                <p>Quản lý danh sách học viên, theo dõi tiến độ và kết quả học tập.</p>
            </div>
        </a>
        
        <a href="{{ route('tro-giang.lop-hoc.danh-sach-bai-tap', $lopHoc->id) }}" class="bg-white rounded-lg shadow overflow-hidden flex flex-col hover:shadow-lg transition duration-150">
            <div class="p-6 flex items-center justify-between border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Bài tập</h3>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $lopHoc->bai_taps_count ?? 0 }}</span>
            </div>
            <div class="p-6 text-gray-600">
                <p>Quản lý các bài tập, chấm điểm và đánh giá bài tập đã nộp.</p>
            </div>
        </a>
        
        <a href="#" class="bg-white rounded-lg shadow overflow-hidden flex flex-col hover:shadow-lg transition duration-150">
            <div class="p-6 flex items-center justify-between border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Bài học</h3>
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $lopHoc->bai_hoc_lops_count ?? 0 }}</span>
            </div>
            <div class="p-6 text-gray-600">
                <p>Danh sách các bài học trong lớp học.</p>
            </div>
        </a>
    </div>
    
    <!-- Thống kê tiến độ học viên -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Tiến độ học viên</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-xl font-bold text-indigo-600">{{ $hoanThanhTyLe }}%</div>
                    <div class="text-sm text-gray-500">Hoàn thành khóa học</div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $hoanThanhTyLe }}%"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-xl font-bold text-blue-600">{{ $diemTrungBinh ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">Điểm trung bình bài tập</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-xl font-bold text-green-600">{{ $baiTapDaNop_count ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Bài tập đã nộp</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-xl font-bold text-yellow-600">{{ $baiTapChuaNop_count ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Bài tập chưa nộp</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bài tập gần đây cần chấm điểm -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-800">Bài tập gần đây cần chấm điểm</h2>
            <a href="{{ route('tro-giang.lop-hoc.danh-sach-bai-tap', $lopHoc->id) }}" class="text-sm font-medium text-red-600 hover:text-red-800">Xem tất cả</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Học viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài tập</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày nộp</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($baiTapGanDay as $baiTap)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $baiTap->hocVien->ho_ten }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $baiTap->baiTap->tieu_de }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($baiTap->ngay_nop)->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tro-giang.bai-tap.show', $baiTap->id) }}" class="text-red-600 hover:text-red-900 mr-3">Xem</a>
                                <a href="{{ route('tro-giang.bai-tap.form-cham-diem', $baiTap->id) }}" class="text-blue-600 hover:text-blue-900">Chấm điểm</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>Không có bài tập nào cần chấm điểm</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 