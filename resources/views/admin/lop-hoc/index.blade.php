@extends('layouts.dashboard')

@section('title', 'Quản lý lớp học')
@section('page-heading', 'Danh sách lớp học')

@php
    $active = 'lop-hoc';
    $role = 'admin';
@endphp

@section('content')
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tổng số lớp</p>
                    <p class="text-xl font-semibold text-gray-700">{{ $tong_lop }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đang diễn ra</p>
                    <p class="text-xl font-semibold text-gray-700">{{ $dang_dien_ra }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sắp diễn ra</p>
                    <p class="text-xl font-semibold text-gray-700">{{ $sap_dien_ra }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Đã kết thúc</p>
                    <p class="text-xl font-semibold text-gray-700">{{ $da_ket_thuc }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Quản lý lớp học</h2>
            <div class="mt-4 md:mt-0 flex">
                <a href="{{ route('admin.lop-hoc.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 disabled:opacity-25 transition ml-2">
                    <i class="fas fa-plus mr-2"></i> Thêm lớp học
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bộ lọc -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <form action="{{ route('admin.lop-hoc.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Tên lớp, mã lớp..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>
            
            <div>
                <label for="khoa_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Khóa học</label>
                <select name="khoa_hoc_id" id="khoa_hoc_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Tất cả khóa học</option>
                    @foreach($khoaHocs as $khoaHoc)
                        <option value="{{ $khoaHoc->id }}" {{ request('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>{{ $khoaHoc->ten }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="sap_dien_ra" {{ request('trang_thai') == 'sap_dien_ra' ? 'selected' : '' }}>Sắp khai giảng</option>
                    <option value="dang_dien_ra" {{ request('trang_thai') == 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="da_ket_thuc" {{ request('trang_thai') == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md">
                    <i class="fas fa-search mr-1"></i> Lọc
                </button>
                <a href="{{ route('admin.lop-hoc.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách lớp học -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Danh sách lớp học ({{ $lopHocs->total() }})</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.lop-hoc.index', array_merge(request()->query(), ['sort' => 'ten', 'direction' => request('direction') == 'asc' && request('sort') == 'ten' ? 'desc' : 'asc'])) }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm {{ request('sort') == 'ten' ? 'bg-gray-100' : 'bg-white' }}">
                        Tên
                        @if(request('sort') == 'ten')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </a>
                    <a href="{{ route('admin.lop-hoc.index', array_merge(request()->query(), ['sort' => 'ngay_bat_dau', 'direction' => request('direction') == 'asc' && request('sort') == 'ngay_bat_dau' ? 'desc' : 'asc'])) }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm {{ request('sort') == 'ngay_bat_dau' ? 'bg-gray-100' : 'bg-white' }}">
                        Ngày bắt đầu
                        @if(request('sort') == 'ngay_bat_dau')
                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lớp
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Khóa học
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giáo viên
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thời gian
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Học viên
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lopHocs as $lopHoc)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700">
                                        <i class="fas fa-chalkboard"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $lopHoc->ten }}</div>
                                        <div class="text-xs text-gray-500">Mã lớp: {{ $lopHoc->ma_lop }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $lopHoc->khoaHoc->ten }}</div>
                                <div class="text-xs text-gray-500">{{ $lopHoc->hinh_thuc_hoc == 'online' ? 'Trực tuyến' : 'Trực tiếp' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $lopHoc->giaoVien->nguoiDung->ho_ten ?? 'Chưa phân công' }}</div>
                                <div class="text-xs text-gray-500">TG: {{ $lopHoc->troGiang->nguoiDung->ho_ten ?? 'Chưa phân công' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $lopHoc->lich_hoc }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <span class="text-blue-600 font-medium">{{ $lopHoc->dangKyHocs->where('trang_thai', 'da_xac_nhan')->count() }}</span>
                                    <span class="mx-1">/</span>
                                    <span>{{ $lopHoc->so_luong_toi_da }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $lopHoc->trang_thai == 'sap_dien_ra' ? 'bg-yellow-100 text-yellow-800' : 
                                      ($lopHoc->trang_thai == 'dang_dien_ra' ? 'bg-green-100 text-green-800' : 
                                       'bg-gray-100 text-gray-800') }}">
                                    {{ $lopHoc->trang_thai == 'sap_dien_ra' ? 'Sắp khai giảng' : 
                                      ($lopHoc->trang_thai == 'dang_dien_ra' ? 'Đang diễn ra' : 'Đã kết thúc') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-end">
                                    <a href="{{ route('admin.lop-hoc.show', $lopHoc->id) }}" class="text-blue-600 hover:text-blue-900" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.lop-hoc.edit', $lopHoc->id) }}" class="text-green-600 hover:text-green-900" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.lop-hoc.danh-sach-hoc-vien', $lopHoc->id) }}" class="text-purple-600 hover:text-purple-900" title="Danh sách học viên">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <form action="{{ route('admin.lop-hoc.destroy', $lopHoc->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp học này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Không tìm thấy lớp học nào phù hợp với điều kiện tìm kiếm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lopHocs->withQueryString()->links() }}
        </div>
    </div>
@endsection
