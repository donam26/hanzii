@extends('layouts.dashboard')

@section('title', 'Quản lý tài chính')
@section('page-heading', 'Quản lý tài chính')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Thống kê tài chính -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Thống kê tài chính</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Đã thanh toán</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($tongDaThanhToan, 0, ',', '.') }} VNĐ</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Cần thanh toán</h3>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($tongCanThanhToan, 0, ',', '.') }} VNĐ</p>
                </div>
            </div>
        </div>

        <!-- Hành động nhanh -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Hành động nhanh</h2>
            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('hoc-vien.tai-chinh.lop-chua-dong-tien') }}" class="inline-flex items-center justify-between px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <span>Xem lớp học chưa đóng tiền</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('hoc-vien.tai-chinh.lich-su') }}" class="inline-flex items-center justify-between px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span>Xem lịch sử thanh toán</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Lớp học chưa đóng tiền -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Lớp học chưa đóng tiền</h2>
                <a href="{{ route('hoc-vien.tai-chinh.lop-chua-dong-tien') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Xem tất cả
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tên lớp
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Khóa học
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Học phí
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lopChuaDongTien as $dangKy)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $dangKy->lopHoc->ten }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $dangKy->lopHoc->khoaHoc->ten }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                {{ number_format($dangKy->lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} VNĐ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('hoc-vien.tai-chinh.form-thanh-toan', $dangKy->id) }}" class="text-red-600 hover:text-red-900">
                                    Thanh toán ngay
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Bạn không có lớp học nào chưa thanh toán
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Giao dịch gần đây -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Giao dịch gần đây</h2>
                <a href="{{ route('hoc-vien.tai-chinh.lich-su') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Xem tất cả
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã giao dịch
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số tiền
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày thanh toán
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $transaction->ma_thanh_toan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($transaction->so_tien, 0, ',', '.') }} VNĐ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->ngay_thanh_toan ? \Carbon\Carbon::parse($transaction->ngay_thanh_toan)->format('d/m/Y') : 'Chưa thanh toán' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->trang_thai == 'thanh_cong')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Thành công
                                </span>
                                @elseif($transaction->trang_thai == 'that_bai')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Thất bại
                                </span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Chờ xử lý
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Bạn chưa có giao dịch nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Lưu ý: Vui lòng thanh toán học phí đúng hạn để đảm bảo tham gia lớp học và nhận đầy đủ tài liệu.
                </p>
            </div>
        </div>
    </div>
@endsection
