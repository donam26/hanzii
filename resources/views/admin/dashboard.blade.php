@extends('layouts.dashboard')

@section('title', 'Trang quản trị')
@section('page-heading', 'Tổng quan')

@php
    $active = 'dashboard';
    $role = 'admin';
@endphp

@section('content')
    <!-- Thống kê tổng quan -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Tổng số học viên -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-red-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tổng số học viên
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $tongSoHocVien }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.hoc-vien.index') }}" class="font-medium text-red-600 hover:text-red-900">
                            Xem tất cả
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lớp học đang hoạt động -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Lớp học đang hoạt động
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $lopHocDangHoatDong }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.lop-hoc.index') }}" class="font-medium text-blue-600 hover:text-blue-900">
                            Xem tất cả
                        </a>
                    </div>
                </div>
            </div>

            <!-- Giáo viên -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tổng số giáo viên
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $tongSoGiaoVien }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.giao-vien.index') }}" class="font-medium text-green-600 hover:text-green-900">
                            Xem tất cả
                        </a>
                    </div>
                </div>
            </div>

            <!-- Doanh thu tháng -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-purple-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Doanh thu tháng
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ number_format($doanhThuThang, 0, ',', '.') }} đ
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.hoc-phi.index') }}" class="font-medium text-purple-600 hover:text-purple-900">
                            Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Lớp học sắp khai giảng -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Lớp học sắp khai giảng</h3>
                <a href="{{ route('admin.lop-hoc.create') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md">
                    <i class="fas fa-plus mr-1"></i> Tạo lớp mới
                </a>
            </div>
            <div class="p-6">
                @if ($lopHocSapKhaiGiang->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach ($lopHocSapKhaiGiang as $lopHoc)
                                <li>
                                    <div class="relative pb-8">
                                        @if (!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <a href="{{ route('admin.lop-hoc.show', $lopHoc->id) }}" class="font-medium text-gray-900">
                                                            {{ $lopHoc->ten }}
                                                        </a>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="mr-2">
                                                            <i class="fas fa-book mr-1"></i> {{ $lopHoc->khoaHoc->ten }}
                                                        </span>
                                                        <span class="mr-2">
                                                            <i class="fas fa-users mr-1"></i> {{ $lopHoc->hocViens->count() }}/{{ $lopHoc->so_luong_toi_da }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-graduation-cap mr-1"></i> {{ $lopHoc->giaoVien->ho_ten ?? 'Chưa phân công' }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-900">
                                                    <p class="font-medium">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Khai giảng trong {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->diffInDays(now()) }} ngày
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('admin.lop-hoc.index', ['trang_thai' => 'sap_dien_ra']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            Xem tất cả lớp sắp khai giảng <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500">Không có lớp học nào sắp khai giảng</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.lop-hoc.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Tạo lớp mới
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Học viên mới đăng ký -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Học viên mới đăng ký</h3>
                <a href="{{ route('admin.hoc-vien.index') }}" class="text-sm text-blue-600 hover:text-blue-900">
                    Xem tất cả <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @if ($hocVienMoiDangKy->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @foreach ($hocVienMoiDangKy as $hocVien)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-700">
                                                {{ strtoupper(substr($hocVien->ho_ten, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $hocVien->ho_ten }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                <i class="fas fa-envelope mr-1"></i> {{ $hocVien->email }}
                                            </p>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.hoc-vien.show', $hocVien->id) }}" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                Xem
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <p class="text-gray-500">Không có học viên mới đăng ký</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chức năng nhanh -->
    <div class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Chức năng nhanh</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.lop-hoc.create') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-md bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Tạo lớp học</h3>
                        <p class="mt-1 text-sm text-gray-500">Tạo lớp học mới cho khóa học</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.khoa-hoc.create') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Tạo khóa học</h3>
                        <p class="mt-1 text-sm text-gray-500">Thêm khóa học mới vào hệ thống</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.hoc-vien.create') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-md bg-red-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Thêm học viên</h3>
                        <p class="mt-1 text-sm text-gray-500">Thêm học viên mới vào hệ thống</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.tai-lieu.create') }}" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-md bg-purple-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Tải tài liệu</h3>
                        <p class="mt-1 text-sm text-gray-500">Thêm tài liệu bổ sung cho các lớp học</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thống kê học phí -->
        <div class="bg-white shadow rounded-lg overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Thống kê học phí theo tháng</h3>
            </div>
            <div class="p-6">
                <div class="h-72">
                    <canvas id="hocPhiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Thông báo mới nhất -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Thông báo mới nhất</h3>
                <a href="{{ route('admin.thong-bao.index') }}" class="text-sm text-blue-600 hover:text-blue-900">
                    Tất cả <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                @if ($thongBaoMoiNhat->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @foreach ($thongBaoMoiNhat as $thongBao)
                                <li class="py-4">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $thongBao->tieu_de }}
                                            </p>
                                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                                {{ Str::limit(strip_tags($thongBao->noi_dung), 100) }}
                                            </p>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <i class="far fa-clock mr-1"></i>
                                                <span>{{ \Carbon\Carbon::parse($thongBao->tao_luc)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('admin.thong-bao.create') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-plus mr-2"></i> Tạo thông báo mới
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-gray-500">Không có thông báo nào</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.thong-bao.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-plus mr-2"></i> Tạo thông báo mới
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dữ liệu biểu đồ học phí
        var hocPhiLabels = @json($hocPhiThang->pluck('thang'));
        var hocPhiValues = @json($hocPhiThang->pluck('tong_tien')->map(function($item) { return $item / 1000000; }));
        
        var hocPhiData = {
            labels: hocPhiLabels,
            datasets: [{
                label: 'Học phí (triệu đồng)',
                data: hocPhiValues,
                backgroundColor: 'rgba(234, 88, 12, 0.2)',
                borderColor: 'rgba(234, 88, 12, 1)',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: 'rgba(234, 88, 12, 1)',
                pointRadius: 4
            }]
        };

        // Cấu hình biểu đồ học phí
        var hocPhiConfig = {
            type: 'line',
            data: hocPhiData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' triệu đồng';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' tr';
                            }
                        }
                    }
                }
            }
        };

        // Vẽ biểu đồ học phí
        var hocPhiCtx = document.getElementById('hocPhiChart').getContext('2d');
        new Chart(hocPhiCtx, hocPhiConfig);
    });
</script>
@endsection 