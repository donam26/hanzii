@extends('layouts.dashboard')

@section('title', 'Thanh toán thành công')
@section('page-heading', 'Thanh toán thành công')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="flex flex-col items-center text-center p-8 bg-white rounded-lg shadow-lg max-w-3xl mx-auto">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Thanh toán thành công!</h2>
        <p class="text-gray-600 mb-8">Thanh toán học phí của bạn đã được ghi nhận.</p>
        
        <div class="w-full bg-gray-50 rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Mã giao dịch</h3>
                    <p class="text-gray-900">{{ $thanhToan->ma_giao_dich }}</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Ngày thanh toán</h3>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('H:i:s d/m/Y') }}</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Lớp học</h3>
                    <p class="text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Khóa học</h3>
                    <p class="text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Phương thức thanh toán</h3>
                    <p class="text-gray-900">VNPay</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Trạng thái</h3>
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Thành công
                    </span>
                </div>
                
                <div class="text-left md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Số tiền thanh toán</h3>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</p>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            @if(isset($thanhToan->hoaDon))
            <a href="{{ route('hoc-vien.tai-chinh.hoa-don', $thanhToan->hoaDon->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Xem hóa đơn
            </a>
            @endif
            
            <a href="{{ route('hoc-vien.lop-hoc.show', $thanhToan->dangKyHoc->lop_hoc_id) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Đi đến lớp học
            </a>
        </div>
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('hoc-vien.tai-chinh.index') }}" class="text-red-600 hover:text-red-800">
            Quay lại quản lý tài chính
        </a>
    </div>
@endsection 