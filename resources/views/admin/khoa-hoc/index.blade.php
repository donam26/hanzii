@extends('layouts.dashboard')

@section('title', 'Quản lý khóa học')
@section('page-heading', 'Danh sách khóa học')

@php
    $active = 'khoa-hoc';
    $role = 'admin';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Quản lý khóa học</h2>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.khoa-hoc.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 disabled:opacity-25 transition">
                    <i class="fas fa-plus-circle mr-2"></i> Thêm khóa học mới
                </a>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <form action="{{ route('admin.khoa-hoc.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Tên khóa học..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
            </div>
            
            <div>
                <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="dang_hoat_dong" {{ request('trang_thai') == 'dang_hoat_dong' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="tam_ngung" {{ request('trang_thai') == 'tam_ngung' ? 'selected' : '' }}>Tạm ngưng</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md">
                    <i class="fas fa-search mr-1"></i> Lọc
                </button>
                <a href="{{ route('admin.khoa-hoc.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách khóa học -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($khoaHocs as $khoaHoc)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="relative">
                    @if($khoaHoc->hinh_anh)
                        <img src="{{ Storage::url($khoaHoc->hinh_anh) }}" alt="{{ $khoaHoc->ten }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-book text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <div class="absolute top-0 right-0 mt-2 mr-2">
                        @if($khoaHoc->trang_thai == 'dang_hoat_dong')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                Đang hoạt động
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                Tạm ngưng
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $khoaHoc->ten }}</h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $khoaHoc->mo_ta_ngan }}</p>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                        <span>{{ number_format($khoaHoc->hoc_phi, 0, ',', '.') }} đ</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="flex flex-col items-center bg-gray-50 p-2 rounded">
                            <span class="text-xs text-gray-500">Thời lượng</span>
                            <span class="font-medium">{{ $khoaHoc->thoi_luong }} buổi</span>
                        </div>
                        <div class="flex flex-col items-center bg-gray-50 p-2 rounded">
                            <span class="text-xs text-gray-500">Lớp đang mở</span>
                            <span class="font-medium">{{ $khoaHoc->lopHocs->where('trang_thai', 'dang_dien_ra')->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.khoa-hoc.show', $khoaHoc->id) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 text-center text-sm font-medium rounded">
                            <i class="fas fa-eye mr-1"></i> Chi tiết
                        </a>
                        <a href="{{ route('admin.khoa-hoc.edit', $khoaHoc->id) }}" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 text-center text-sm font-medium rounded">
                            <i class="fas fa-edit mr-1"></i> Sửa
                        </a>
                        <form action="{{ route('admin.lop-hoc.create') }}" method="GET" class="flex-1">
                            <input type="hidden" name="khoa_hoc_id" value="{{ $khoaHoc->id }}">
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 text-sm font-medium rounded">
                                <i class="fas fa-plus mr-1"></i> Mở lớp
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i> Cập nhật: {{ \Carbon\Carbon::parse($khoaHoc->updated_at)->format('d/m/Y') }}
                        </span>
                        <form action="{{ route('admin.khoa-hoc.destroy', $khoaHoc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khóa học này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white shadow rounded-lg p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có khóa học nào</h3>
                <p class="text-gray-600 mb-6">Bạn chưa tạo khóa học nào. Hãy thêm khóa học mới để bắt đầu.</p>
                <a href="{{ route('admin.khoa-hoc.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white">
                    <i class="fas fa-plus-circle mr-2"></i> Thêm khóa học mới
                </a>
            </div>
        @endforelse
    </div>
    
    <!-- Phân trang -->
    <div class="mt-6">
        {{ $khoaHocs->appends(request()->query())->links() }}
    </div>
    
    <!-- Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-book text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Tổng khóa học</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $tongKhoaHoc }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Tổng lớp đang học</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $tongLopDangHoc }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <i class="fas fa-user-graduate text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Học viên đang học</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $tongHocVienDangHoc }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Doanh thu tháng</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($doanhThuThang, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>
    </div>
@endsection 