@extends('layouts.dashboard')

@section('title', 'Thống kê học viên')

@section('page-heading', 'Thống kê học viên')

@php
    $active = 'thong-ke-hoc-vien';
    $role = 'admin';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Thống kê học viên</h2>
            <div class="mt-4 md:mt-0">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-700 disabled:opacity-25 transition">
                    <i class="fas fa-file-excel mr-2"></i> Xuất Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Thẻ thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-user-graduate text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Tổng số học viên</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $tongHocVien ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-user-check text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Học viên đang học</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $hocVienDangHoc ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Học viên mới tháng này</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $hocVienMoiThangNay ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-user-clock text-lg"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-sm font-medium text-gray-500">Đăng ký chờ xử lý</h3>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $dangKyChoXuLy ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ thống kê -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Biểu đồ thống kê học viên theo thời gian</h3>
        </div>
        <div class="p-6">
            <canvas id="hocVienChart" height="100"></canvas>
        </div>
    </div>

    <!-- Thống kê học viên theo khóa học -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Phân bố học viên theo khóa học</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khóa học
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số lớp
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số học viên
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tỷ lệ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($khoaHocStats ?? [] as $khoaHoc)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $khoaHoc->ten }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $khoaHoc->so_lop }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $khoaHoc->so_hoc_vien }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $khoaHoc->ty_le }}%"></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-900">{{ $khoaHoc->ty_le }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Không có dữ liệu thống kê
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Học viên mới đăng ký -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Học viên mới đăng ký gần đây</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Họ tên
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số điện thoại
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khóa học
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày đăng ký
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($hocVienMoi ?? [] as $hocVien)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $hocVien->nguoiDung->ho }} {{ $hocVien->nguoiDung->ten }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $hocVien->nguoiDung->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $hocVien->nguoiDung->so_dien_thoai }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $hocVien->lopHoc->khoaHoc->ten ?? 'Chưa tham gia khóa học' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $hocVien->tao_luc->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $hocVien->trang_thai == 'hoat_dong' ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ học viên theo thời gian
        const ctx = document.getElementById('hocVienChart').getContext('2d');
        const hocVienChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                datasets: [{
                    label: 'Học viên đăng ký',
                    data: {!! json_encode($duLieuBieuDo ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!},
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Số lượng học viên đăng ký theo tháng'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection 