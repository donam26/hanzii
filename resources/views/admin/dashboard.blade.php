@extends('layouts.dashboard')

@section('title', 'Trang Quản Trị')
@section('page-heading', 'Bảng Điều Khiển')

@php
$active = 'dashboard';
$role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium opacity-80">Tổng học viên</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($tongHocVien) }}</p>
                    <p class="text-sm mt-2">Tháng này: +{{ number_format($hocVienMoiThang) }}</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-lg p-3">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium opacity-80">Doanh thu tháng</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($doanhThuThang) }}đ</p>
                    <p class="text-sm mt-2">Năm nay: {{ number_format($doanhThuNam) }}đ</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-lg p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium opacity-80">Tổng lớp học</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($tongLopHoc) }}</p>
                    <p class="text-sm mt-2">Đang hoạt động: {{ number_format($lopHocDangHoatDong) }}</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-lg p-3">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium opacity-80">Tổng nhân viên</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($tongNhanVien) }}</p>
                    <p class="text-sm mt-2">GV: {{ $tongGiaoVien }} | TG: {{ $tongTroGiang }}</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-lg p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Biểu đồ thống kê -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Biểu đồ học viên đăng ký -->
        <div class="bg-white rounded-lg shadow-md" x-data="hocVienChart()">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                    Học viên đăng ký theo tháng
                </h2>
            </div>
            <div class="p-4">
                <canvas id="hocVienChart" height="300"></canvas>
            </div>
        </div>
        
        <!-- Biểu đồ doanh thu -->
        <div class="bg-white rounded-lg shadow-md" x-data="doanhThuChart()">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                    Doanh thu theo tháng
                </h2>
            </div>
            <div class="p-4">
                <canvas id="doanhThuChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Thống kê khóa học & lớp học -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lớp học sắp khai giảng -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-md lg:col-span-2 border border-indigo-200">
            <div class="p-4 border-b border-indigo-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-indigo-800">
                    <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                    Lớp học sắp khai giảng
                </h2>
                <a href="{{ route('admin.lop-hoc.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-indigo-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Mã lớp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Tên lớp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Khóa học</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Giáo viên</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-indigo-500 uppercase tracking-wider">Ngày khai giảng</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-indigo-200">
                            @forelse($danhSachLopHocSapKhaiGiang as $lopHoc)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $lopHoc->ma_lop }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.lop-hoc.show', $lopHoc->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                            {{ $lopHoc->ten }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $lopHoc->khoaHoc->ten }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $lopHoc->giaoVien->nguoiDung->ho_ten ?? $lopHoc->giaoVien->nguoiDung->ho . ' ' . $lopHoc->giaoVien->nguoiDung->ten }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-sm text-gray-500 text-center">
                                        Không có lớp học nào sắp khai giảng
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Thống kê khóa học -->
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md h-full border border-amber-200">
            <div class="p-4 border-b border-amber-200">
                <h2 class="text-lg font-semibold text-amber-800">
                    <i class="fas fa-book mr-2 text-amber-500"></i>
                    Khóa học phổ biến
                </h2>
            </div>
            <div class="p-4">
                @forelse($thongKeHocVienTheoKhoaHoc as $khoaHoc)
                    <div class="mb-4 last:mb-0">
                        <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                            <span>{{ $khoaHoc->ten }}</span>
                            <span>{{ $khoaHoc->so_hoc_vien }} học viên</span>
                        </div>
                        <div class="w-full bg-amber-200 rounded-full h-2.5">
                            <div class="bg-amber-600 h-2.5 rounded-full" style="width: {{ min(100, $khoaHoc->so_hoc_vien / 10 * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $khoaHoc->so_lop_hoc }} lớp học</p>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">Chưa có dữ liệu khóa học</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Danh sách yêu cầu và thanh toán gần đây -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Đăng ký học mới -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-md h-full border border-orange-200">
            <div class="p-4 border-b border-orange-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-orange-800">
                    <i class="fas fa-clipboard-list mr-2 text-orange-500"></i>
                    Đăng ký học chờ duyệt
                </h2>
                <a href="{{ route('admin.thanh-toan.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-4">
                <div class="divide-y divide-orange-200">
                    @forelse($dangKyHocMoi as $dangKy)
                        <div class="py-3 flex items-center">
                            <div class="h-10 w-10 rounded-full bg-orange-200 flex items-center justify-center text-orange-600 mr-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $dangKy->hocVien->nguoiDung->ho . ' ' . $dangKy->hocVien->nguoiDung->ten }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $dangKy->lopHoc->ten }} ({{ $dangKy->lopHoc->khoaHoc->ten }})
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($dangKy->ngay_dang_ky)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <p class="text-gray-500">Không có đăng ký mới</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Thanh toán gần đây -->
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg shadow-md h-full border border-emerald-200">
            <div class="p-4 border-b border-emerald-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-emerald-800">
                    <i class="fas fa-file-invoice-dollar mr-2 text-emerald-500"></i>
                    Thanh toán gần đây
                </h2>
                <a href="{{ route('admin.thanh-toan.index') }}" class="text-sm text-emerald-600 hover:text-emerald-800">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-4">
                <div class="divide-y divide-emerald-200">
                    @forelse($thanhToanGanDay as $thanhToan)
                        <div class="py-3 flex items-center">
                            <div class="h-10 w-10 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-600 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho . ' ' . $thanhToan->dangKyHoc->hocVien->nguoiDung->ten }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $thanhToan->dangKyHoc->lopHoc->ten }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-emerald-600">{{ number_format($thanhToan->so_tien) }}đ</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <p class="text-gray-500">Không có thanh toán mới</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Học viên mới đăng ký -->
    <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-lg shadow-md border border-sky-200">
        <div class="p-4 border-b border-sky-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-sky-800">
                <i class="fas fa-users mr-2 text-sky-500"></i>
                Học viên mới đăng ký
            </h2>
            <a href="{{ route('admin.hoc-vien.index') }}" class="text-sm text-sky-600 hover:text-sky-800">
                Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-sky-200">
                <thead class="bg-sky-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Học viên</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Điện thoại</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Ngày đăng ký</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-sky-200">
                    @forelse($hocVienMoiDangKy as $hocVien)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $hocVien->ho_ten }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $hocVien->email }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $hocVien->dien_thoai }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($hocVien->ngay_dang_ky)->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.hoc-vien.show', $hocVien->id) }}" class="text-sky-600 hover:text-sky-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-sm text-gray-500 text-center">
                                Không có học viên mới
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom styles for charts */
    .chart-container {
        height: 300px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function hocVienChart() {
        return {
            init() {
                const data = @json($thongKeHocVienTheoThang);
                const labels = data.map(item => item.thang);
                const values = data.map(item => item.so_luong);
                
                const ctx = document.getElementById('hocVienChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Học viên đăng ký',
                            data: values,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }
        }
    }
    
    function doanhThuChart() {
        return {
            init() {
                const data = @json($thongKeHocPhiTheoThang);
                const labels = data.map(item => item.thang);
                const values = data.map(item => item.doanh_thu);
                
                const ctx = document.getElementById('doanhThuChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh thu (VNĐ)',
                            data: values,
                            backgroundColor: 'rgba(16, 185, 129, 0.5)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return new Intl.NumberFormat('vi-VN').format(context.raw) + 'đ';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush
