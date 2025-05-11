@extends('layouts.dashboard')

@section('title', 'Quản lý thanh toán học phí')
@section('page-heading', 'Quản lý thanh toán học phí')

@php
    $active = 'thanh-toan-hoc-phi';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md border border-blue-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 text-white rounded-lg">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($thanhToans->total()) }}</h2>
                    <p class="text-sm text-gray-600">Tổng thanh toán</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md border border-green-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 text-white rounded-lg">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($thanhToans->where('trang_thai', 'da_thanh_toan')->count()) }}</h2>
                    <p class="text-sm text-gray-600">Đã thanh toán</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-md border border-red-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-500 text-white rounded-lg">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($thanhToans->where('trang_thai', 'chua_thanh_toan')->count()) }}</h2>
                    <p class="text-sm text-gray-600">Chưa thanh toán</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md border border-amber-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-amber-500 text-white rounded-lg">
                    <i class="fas fa-sync text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($thanhToans->where('trang_thai', 'dang_xu_ly')->count()) }}</h2>
                    <p class="text-sm text-gray-600">Đang xử lý</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hành động -->
    <div class="flex justify-between">
        <div></div>
        <a href="{{ route('admin.thanh-toan-hoc-phi.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
            <i class="fas fa-plus mr-2"></i> Tạo mới
        </a>
    </div>

    <!-- Danh sách thanh toán học phí -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã thanh toán</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Học viên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($thanhToans as $index => $thanhToan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $index + 1 }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $thanhToan->ma_thanh_toan }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($thanhToan->hocVien && $thanhToan->hocVien->nguoiDung)
                                    {{ $thanhToan->hocVien->nguoiDung->ho }} {{ $thanhToan->hocVien->nguoiDung->ten }}
                                @else
                                    <span class="text-red-500">Không có thông tin</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($thanhToan->hocVien && $thanhToan->hocVien->nguoiDung)
                                    {{ $thanhToan->hocVien->nguoiDung->email }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($thanhToan->lopHoc)
                                    {{ $thanhToan->lopHoc->ten }}
                                @else
                                    <span class="text-red-500">Không có thông tin</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($thanhToan->lopHoc)
                                    {{ $thanhToan->lopHoc->ma_lop }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VND</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($thanhToan->trang_thai == 'da_thanh_toan')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đã thanh toán
                                </span>
                            @elseif($thanhToan->trang_thai == 'chua_thanh_toan')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Chưa thanh toán
                                </span>
                            @elseif($thanhToan->trang_thai == 'dang_xu_ly')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Đang xử lý
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Không xác định
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.thanh-toan-hoc-phi.show', $thanhToan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.thanh-toan-hoc-phi.edit', $thanhToan->id) }}" class="text-amber-600 hover:text-amber-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Phân trang -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $thanhToans->links() }}
        </div>
    </div>
</div>
@endsection 