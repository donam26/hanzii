@extends('layouts.dashboard')

@section('title', 'Thanh toán học phí')
@section('page-heading', 'Thanh toán học phí')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6">
        <a href="{{ route('hoc-vien.tai-chinh.lop-chua-dong-tien') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
            </svg>
            Quay lại
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thông tin thanh toán -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Thông tin thanh toán</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Lớp học</h3>
                            <p class="text-gray-800 font-semibold">{{ $dangKyHoc->lopHoc->ten }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Mã lớp</h3>
                            <p class="text-gray-800">{{ $dangKyHoc->lopHoc->ma_lop }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Khóa học</h3>
                            <p class="text-gray-800">{{ $dangKyHoc->lopHoc->khoaHoc->ten }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Thời gian học</h3>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($dangKyHoc->lopHoc->ngay_bat_dau)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dangKyHoc->lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Hình thức học</h3>
                            <p class="text-gray-800">{{ $dangKyHoc->lopHoc->hinh_thuc_hoc === 'online' ? 'Trực tuyến' : 'Trực tiếp' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Ngày đăng ký</h3>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($dangKyHoc->ngay_dang_ky)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <hr class="my-6">
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Học phí</h3>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($dangKyHoc->lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} VNĐ</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Trạng thái</h3>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Chờ thanh toán
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Phương thức thanh toán -->
        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Phương thức thanh toán</h2>
                    
                    <form action="{{ route('hoc-vien.tai-chinh.thanh-toan-vnpay', $dangKyHoc->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <p class="text-gray-600 mb-4">Thanh toán học phí qua cổng thanh toán VNPay an toàn và bảo mật.</p>
                            
                            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <input id="vnpay" name="payment_method" type="radio" class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" checked>
                                    <label for="vnpay" class="ml-3 flex items-center">
                                        <span class="font-medium text-gray-800 mr-3">VNPay</span>
                                        <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPay" class="h-8">
                                    </label>
                                </div>
                                <div class="ml-7 mt-2 text-sm text-gray-500">
                                    <p>Thanh toán qua cổng VNPay với nhiều loại thẻ và ngân hàng khác nhau</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between mb-2">
                                <span class="font-medium text-gray-700">Tổng tiền:</span>
                                <span class="font-bold text-gray-900">{{ number_format($dangKyHoc->lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} VNĐ</span>
                            </div>
                            
                            <button type="submit" class="w-full mt-4 bg-red-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Thanh toán ngay
                            </button>
                            
                            <p class="mt-3 text-xs text-gray-500 text-center">
                                Bằng cách nhấn vào nút "Thanh toán ngay", bạn đồng ý với <a href="#" class="text-red-600 hover:text-red-800">Điều khoản thanh toán</a> của chúng tôi.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Thanh toán học phí là điều kiện cần thiết để tham gia lớp học. Vui lòng hoàn tất thanh toán trước khi lớp học bắt đầu.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 