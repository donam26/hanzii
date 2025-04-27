@extends('layouts.dashboard')

@section('title', 'Yêu cầu thanh toán học phí')
@section('page-heading', 'Thanh toán học phí')

@php
    $active = 'lop-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Thông tin thanh toán</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-base font-medium text-gray-700 mb-4">Thông tin lớp học</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tên lớp học</dt>
                            <dd class="text-base text-gray-900">{{ $lopHoc->ten }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Khóa học</dt>
                            <dd class="text-base text-gray-900">{{ $lopHoc->khoaHoc->ten }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thời gian bắt đầu</dt>
                            <dd class="text-base text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thời gian kết thúc dự kiến</dt>
                            <dd class="text-base text-gray-900">{{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h4 class="text-base font-medium text-gray-700 mb-4">Thông tin học phí</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Học phí cần thanh toán</dt>
                            <dd class="text-lg font-bold text-red-600">{{ number_format($dangKyHoc->hoc_phi, 0, ',', '.') }} VNĐ</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trạng thái thanh toán</dt>
                            <dd class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Chưa thanh toán
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Hạn chót thanh toán</dt>
                            <dd class="text-base text-gray-900">
                                @if($dangKyHoc->han_thanh_toan)
                                    {{ \Carbon\Carbon::parse($dangKyHoc->han_thanh_toan)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($dangKyHoc->han_thanh_toan)->isPast())
                                        <span class="text-red-600 text-sm">(Đã quá hạn)</span>
                                    @endif
                                @else
                                    Không giới hạn
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 text-sm text-blue-700">
                        <p>Thanh toán học phí để được tham gia lớp học và truy cập vào tất cả tài liệu, bài giảng và các hoạt động của lớp.</p>
                    </div>
                </div>
            </div>

            @if($thanhToanDangXuLy)
                <div class="mb-6 rounded-md bg-yellow-50 p-4 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">Bạn đang có một yêu cầu thanh toán đang xử lý. Vui lòng chờ xác nhận hoặc tiếp tục thanh toán.</p>
                            <p class="text-sm text-yellow-700 mt-2"><strong>Mã thanh toán:</strong> {{ $thanhToanDangXuLy->ma_thanh_toan }}</p>
                            <p class="text-sm text-yellow-700"><strong>Thời gian tạo:</strong> {{ $thanhToanDangXuLy->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form id="vnpay-form" action="{{ route('hoc-vien.thanh-toan.xu-ly', $dangKyHoc->id) }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <h4 class="text-base font-medium text-gray-700 mb-4">Phương thức thanh toán</h4>
                    
                    <div class="bg-white border border-gray-200 rounded-md p-4">
                        <div class="flex items-center">
                            <input id="payment-method-vnpay" name="payment_method" type="radio" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <label for="payment-method-vnpay" class="ml-3 flex items-center">
                                <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPAY" class="h-8">
                                <span class="ml-2 block text-sm text-gray-700">Thanh toán trực tuyến qua VNPAY</span>
                            </label>
                        </div>
                        <div class="mt-3 pl-7">
                            <p class="text-sm text-gray-500">Thanh toán trực tuyến an toàn qua cổng VNPAY bằng thẻ ATM, thẻ quốc tế hoặc ví điện tử.</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="text-base font-medium text-gray-700 mb-4">Thông tin người thanh toán</h4>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="fullname" id="fullname" value="{{ auth()->user()->ho_ten }}" required
                                class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" required
                                class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone" value="{{ auth()->user()->dien_thoai }}" required
                                class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-base font-medium text-gray-700">Tổng tiền</span>
                        <span class="text-2xl font-bold text-red-600">{{ number_format($dangKyHoc->hoc_phi, 0, ',', '.') }} VNĐ</span>
                    </div>
                    
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('hoc-vien.lop-hoc.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Quay lại
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Thanh toán ngay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 