@extends('layouts.dashboard')

@section('title', 'Quản lý lớp học')
@section('page-heading', 'Quản lý lớp học')

@php
    $active = 'lop-hoc';
    $role = 'admin';
@endphp

@section('styles')
<style>
    @media (max-width: 640px) {
        .search-form-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .search-form-buttons > * {
            margin-top: 0.5rem;
            margin-left: 0 !important;
            width: 100%;
        }
        
        .search-form-buttons > *:first-child {
            margin-top: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md border border-blue-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 text-white rounded-lg">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($tong_lop) }}</h2>
                    <p class="text-sm text-gray-600">Tổng số lớp học</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md border border-green-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 text-white rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($dang_dien_ra) }}</h2>
                    <p class="text-sm text-gray-600">Lớp đang diễn ra</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md border border-amber-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-amber-500 text-white rounded-lg">
                    <i class="fas fa-hourglass-start text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($sap_khai_giang) }}</h2>
                    <p class="text-sm text-gray-600">Lớp sắp khai giảng</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md border border-purple-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-500 text-white rounded-lg">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($da_ket_thuc) }}</h2>
                    <p class="text-sm text-gray-600">Lớp đã kết thúc</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thống kê bổ sung và tác vụ nhanh -->
    <div class="flex flex-col sm:flex-row sm:justify-between">
        <div class="mb-4 sm:mb-0">
            <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users text-indigo-500 mr-2"></i>
                    <span>Tổng học viên: <strong>{{ number_format($tong_hoc_vien) }}</strong></span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-user-tie text-indigo-500 mr-2"></i>
                    <span>Tổng giáo viên: <strong>{{ number_format($tong_giao_vien) }}</strong></span>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.lop-hoc.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                <i class="fas fa-plus mr-2"></i> Thêm lớp học
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white p-4 rounded-lg shadow-md">
        <form action="{{ route('admin.lop-hoc.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div class="min-w-max col-span-1 md:col-span-2 lg:col-span-1">
                <label for="search" class="block text-sm font-medium text-gray-700">Tìm kiếm</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Tên lớp, mã lớp">
                </div>
            </div>
            <div class="w-full">
                <label for="khoa_hoc_id" class="block text-sm font-medium text-gray-700">Khóa học</label>
                <select id="khoa_hoc_id" name="khoa_hoc_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                    <option value="">Tất cả khóa học</option>
                    @foreach($khoaHocs as $khoaHoc)
                        <option value="{{ $khoaHoc->id }}" {{ request('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                            {{ $khoaHoc->ten }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full">
                <label for="trang_thai" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                    <option value="tat_ca">Tất cả trạng thái</option>
                    <option value="sap_khai_giang" {{ request('trang_thai') == 'sap_khai_giang' ? 'selected' : '' }}>Sắp khai giảng</option>
                    <option value="dang_dien_ra" {{ request('trang_thai') == 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="da_ket_thuc" {{ request('trang_thai') == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="flex space-x-2 col-span-1 lg:justify-end search-form-buttons">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 w-full md:w-auto">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
                <a href="{{ route('admin.lop-hoc.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full md:w-auto">
                    <i class="fas fa-redo mr-2"></i> Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách lớp học -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khóa học</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giáo viên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lopHocs as $lopHoc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $lopHoc->ten }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Mã: {{ $lopHoc->ma_lop }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $lopHoc->khoaHoc->ten }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $lopHoc->giaoVien->nguoiDung->ho_ten ?? 'Chưa phân công' }}</div>
                            <div class="text-xs text-gray-500">TG: {{ $lopHoc->troGiang->nguoiDung->ho_ten ?? 'Chưa phân công' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $lopHoc->hinh_thuc_hoc == 'online' ? 'Trực tuyến' : 'Tại trung tâm' }}</div>
                        </td>
                       
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lopHoc->trang_thai == 'sap_khai_giang')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Sắp khai giảng
                                </span>
                            @elseif($lopHoc->trang_thai == 'dang_dien_ra' )
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đang diễn ra
                                </span>
                            @elseif($lopHoc->trang_thai == 'da_ket_thuc' || $lopHoc->trang_thai == 'da_hoan_thanh')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Đã kết thúc
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $lopHoc->trang_thai }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.lop-hoc.show', $lopHoc->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.lop-hoc.edit', $lopHoc->id) }}" class="text-amber-600 hover:text-amber-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.lop-hoc.danh-sach-hoc-vien', $lopHoc->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-users"></i>
                            </a>
                            <form action="{{ route('admin.lop-hoc.destroy', $lopHoc->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bạn có chắc chắn muốn xóa lớp học này?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="my-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                </svg>
                                <p class="mt-2 text-base text-gray-900">Không tìm thấy lớp học nào</p>
                                <p class="mt-1 text-sm text-gray-500">Hãy thử thay đổi bộ lọc hoặc thêm lớp học mới.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Phân trang -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $lopHocs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
