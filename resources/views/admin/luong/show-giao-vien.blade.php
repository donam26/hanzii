@extends('layouts.dashboard')

@section('title', 'Chi tiết lương giáo viên')
@section('page-heading', 'Chi tiết lương giáo viên')

@php
    $active = 'luong';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Hiển thị thông báo -->
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Thành công!</strong>
        <span class="block sm:inline ml-1">{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Lỗi!</strong>
        <span class="block sm:inline ml-1">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Chi tiết lương giáo viên #{{ $luongGiaoVien->id }}</h2>
        <a href="{{ route('admin.luong.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Thông tin cơ bản
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Chi tiết thông tin thanh toán lương
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Giáo viên
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($luongGiaoVien->giaoVien && $luongGiaoVien->giaoVien->nguoiDung)
                            {{ $luongGiaoVien->giaoVien->nguoiDung->ho }} {{ $luongGiaoVien->giaoVien->nguoiDung->ten }}
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $luongGiaoVien->giaoVien->nguoiDung->email }}
                            </div>
                        @else
                            <span class="text-red-500">Không có thông tin giáo viên</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Lớp học
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($luongGiaoVien->lopHoc)
                            {{ $luongGiaoVien->lopHoc->ten }}
                            <div class="text-xs text-gray-500 mt-1">
                                Mã lớp: {{ $luongGiaoVien->lopHoc->ma_lop }}
                            </div>
                        @else
                            <span class="text-red-500">Không có thông tin lớp học</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Số tiền thanh toán
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="text-lg font-bold text-gray-900">{{ number_format($luongGiaoVien->so_tien, 0, ',', '.') }} VNĐ</span>
                        <div class="text-xs text-gray-500 mt-1">
                            Tỷ lệ: {{ $luongGiaoVien->phan_tram }}%
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Trạng thái
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($luongGiaoVien->trang_thai == 'da_thanh_toan')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Đã thanh toán
                            </span>
                            @if($luongGiaoVien->ngay_thanh_toan)
                                <div class="text-xs text-gray-500 mt-1">
                                    Ngày thanh toán: {{ \Carbon\Carbon::parse($luongGiaoVien->ngay_thanh_toan)->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Chưa thanh toán
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Thời gian
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-gray-500">Tháng tính lương:</span>
                                <div>{{ $luongGiaoVien->thang ? \Carbon\Carbon::parse($luongGiaoVien->thang)->format('m/Y') : 'Không xác định' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Ngày tạo:</span>
                                <div>{{ \Carbon\Carbon::parse($luongGiaoVien->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Ghi chú
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $luongGiaoVien->ghi_chu ?? 'Không có ghi chú' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        @if($luongGiaoVien->trang_thai != 'da_thanh_toan')
            <form action="{{ route('admin.luong.thanh-toan-giao-vien', $luongGiaoVien->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-check-circle mr-2"></i> Xác nhận đã thanh toán
                </button>
            </form>
     
        @endif
    </div>
</div>
@endsection 