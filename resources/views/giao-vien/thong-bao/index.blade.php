@extends('layouts.dashboard')

@section('title', 'Quản lý thông báo')
@section('page-heading', 'Quản lý thông báo')

@php
    $active = 'thong_bao';
    $role = 'giao_vien';
    
    function formatFileSize($size) {
        if (!$size) return '0 B';
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1024*1024) {
            return round($size/1024, 2) . ' KB';
        } elseif ($size < 1024*1024*1024) {
            return round($size/(1024*1024), 2) . ' MB';
        } else {
            return round($size/(1024*1024*1024), 2) . ' GB';
        }
    }
@endphp

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Danh sách thông báo</h1>
            <p class="mt-1 text-sm text-gray-600">Quản lý thông báo cho các lớp học của bạn</p>
        </div>
        <a href="{{ route('giao-vien.thong-bao.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Tạo thông báo mới
        </a>
    </div>
    
    <!-- Lọc thông báo -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="p-4">
            <form action="{{ route('giao-vien.thong-bao.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="w-full md:w-auto flex-grow">
                    <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Lớp học</label>
                    <select name="lop_hoc_id" id="lop_hoc_id" class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tất cả lớp học</option>
                        @foreach($lopHocs as $lop)
                            <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>
                                {{ $lop->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-auto flex-grow">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Tìm theo tiêu đề..." class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div class="w-full md:w-auto flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                    
                    @if(request('lop_hoc_id') || request('search'))
                        <a href="{{ route('giao-vien.thong-bao.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-times mr-2"></i> Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <!-- Danh sách thông báo -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if(count($thongBaos) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tiêu đề
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lớp học
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày tạo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File đính kèm
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($thongBaos as $thongBao)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $thongBao->tieu_de }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $thongBao->lopHoc->ten ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $thongBao->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($thongBao->file_path)
                                        <a href="{{ asset('storage/' . $thongBao->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 flex items-center">
                                            <i class="far fa-file mr-1"></i>
                                            <span class="text-sm">{{ $thongBao->ten_file ?? 'Tải xuống' }}</span>
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-500">Không có</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('giao-vien.thong-bao.show', $thongBao->id) }}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        <a href="{{ route('giao-vien.thong-bao.edit', $thongBao->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Chỉnh sửa">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <form action="{{ route('giao-vien.thong-bao.destroy', $thongBao->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $thongBaos->appends(request()->query())->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <div class="text-gray-500">
                    <i class="far fa-bell-slash text-4xl mb-3"></i>
                    <p>Không có thông báo nào.</p>
                    @if(request('lop_hoc_id') || request('search'))
                        <p class="mt-1">Hãy thử thay đổi bộ lọc hoặc tạo thông báo mới.</p>
                    @else
                        <p class="mt-1">Hãy tạo thông báo mới cho lớp học của bạn.</p>
                    @endif
                </div>
                <div class="mt-4">
                    <a href="{{ route('giao-vien.thong-bao.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i> Tạo thông báo mới
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection 