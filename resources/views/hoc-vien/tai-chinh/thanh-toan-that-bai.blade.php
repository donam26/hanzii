@extends('layouts.dashboard')

@section('title', 'Thanh toán thất bại')
@section('page-heading', 'Thanh toán thất bại')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="flex flex-col items-center text-center p-8 bg-white rounded-lg shadow-lg max-w-3xl mx-auto">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Thanh toán thất bại!</h2>
        <p class="text-gray-600 mb-8">Đã xảy ra lỗi trong quá trình thanh toán. Vui lòng thử lại.</p>
        
        <div class="w-full bg-gray-50 rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Mã thanh toán</h3>
                    <p class="text-gray-900">{{ $thanhToan->ma_thanh_toan }}</p>
                </div>
                
                <div class="text-left">
                    <h3 class="text-sm font-medium text-gray-500">Thời gian</h3>
                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($thanhToan->created_at)->format('H:i:s d/m/Y') }}</p>
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
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Thất bại
                    </span>
                </div>
                
                <div class="text-left md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Số tiền</h3>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</p>
                </div>
                
                @if($thanhToan->mo_ta)
                <div class="text-left md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Lý do thất bại</h3>
                    <p class="text-gray-900">{{ $thanhToan->mo_ta }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('hoc-vien.tai-chinh.form-thanh-toan', $thanhToan->dangKyHoc->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Thử lại
            </a>
            
            <a href="{{ route('hoc-vien.tai-chinh.lop-chua-dong-tien') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Quay lại danh sách
            </a>
        </div>
    </div>
    
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 max-w-3xl mx-auto">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Nếu bạn đã bị trừ tiền nhưng nhận thông báo thanh toán thất bại, vui lòng liên hệ với chúng tôi qua số điện thoại <span class="font-medium">0123.456.789</span> hoặc email <span class="font-medium">hotro@hanzii.com</span> để được hỗ trợ.
                </p>
            </div>
        </div>
    </div>
@endsection 