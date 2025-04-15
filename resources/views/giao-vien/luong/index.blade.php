@extends('layouts.dashboard')

@section('title', 'Quản lý lương')
@section('page-heading', 'Quản lý lương')

@php
    $active = 'luong';
    $role = 'giao_vien';
@endphp

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý lương</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-blue-500">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-600 uppercase mb-1">
                            Tổng lương đã nhận
                        </div>
                        <div class="text-xl font-bold text-gray-800">{{ number_format($tongLuongDaNhan, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-green-600 uppercase mb-1">
                            Tổng lương chờ thanh toán
                        </div>
                        <div class="text-xl font-bold text-gray-800">{{ number_format($tongLuongChoThanhToan, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-indigo-500">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-indigo-600 uppercase mb-1">
                            Số lớp học đang dạy
                        </div>
                        <div class="text-xl font-bold text-gray-800">{{ $soLopDangDay }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-yellow-500">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-yellow-600 uppercase mb-1">
                            Tổng số lớp đã dạy
                        </div>
                        <div class="text-xl font-bold text-gray-800">{{ $soLopDaDay }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Danh sách lương</h2>
        </div>
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khóa học</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hệ số</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng lương</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thanh toán</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($luongs as $luong)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $luong->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $luong->lopHoc->ten }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $luong->lopHoc->khoaHoc->ten }}</td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $luong->vai_tro }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $luong->vaiTro->he_so_luong }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($luong->tong_luong, 0, ',', '.') }} VNĐ</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($luong->trang_thai == 'cho_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ thanh toán</span>
                                @elseif($luong->trang_thai == 'da_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã thanh toán</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $luong->ngay_thanh_toan ? $luong->ngay_thanh_toan->format('d/m/Y') : 'Chưa thanh toán' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('giao-vien.luong.show', $luong->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 hover:bg-indigo-200 px-3 py-1 rounded-md">
                                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $luongs->links() }}
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Biểu đồ lương theo tháng</h2>
        </div>
        <div class="p-6">
            <div id="luong-chart-container" class="chart-container" data-luong="{{ htmlspecialchars(json_encode($luongTheoThang)) }}">
                <canvas id="luongChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy dữ liệu từ data attribute
        const chartContainer = document.getElementById('luong-chart-container');
        const luongData = JSON.parse(chartContainer.dataset.luong);
        
        // Cấu hình biểu đồ
        const ctx = document.getElementById('luongChart').getContext('2d');
        const myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: luongData.map(item => item.thang + '/' + item.nam),
                datasets: [{
                    label: "Lương (VNĐ)",
                    backgroundColor: "#4F46E5",
                    hoverBackgroundColor: "#3730A3",
                    borderColor: "#4F46E5",
                    data: luongData.map(item => item.tong_luong),
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        maxBarThickness: 25,
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ' VNĐ';
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            return "Lương: " + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + " VNĐ";
                        }
                    }
                },
            }
        });
    });
</script>
@endpush 