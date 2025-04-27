@extends('layouts.dashboard')

@section('title', 'Thanh toán thất bại')
@section('page-heading', 'Thanh toán thất bại')

@php
    $active = 'khoa-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-red-100 text-red-600 text-3xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Thanh toán thất bại!</h2>
        <p class="text-gray-600 mb-8">Rất tiếc, giao dịch thanh toán của bạn đã bị hủy hoặc không thành công.</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Chi tiết thanh toán</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mã thanh toán</dt>
                            <dd class="text-base text-gray-900">{{ $thanhToan->ma_thanh_toan }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thời gian tạo</dt>
                            <dd class="text-base text-gray-900">{{ \Carbon\Carbon::parse($thanhToan->created_at)->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phương thức thanh toán</dt>
                            <dd class="text-base text-gray-900">VNPAY</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tên lớp học</dt>
                            <dd class="text-base text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Khóa học</dt>
                            <dd class="text-base text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Số tiền</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $thanhToan->trang_thai == 'da_huy' ? 'Đã hủy' : 'Thất bại' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            @if($thanhToan->ghi_chu)
                                {{ $thanhToan->ghi_chu }}
                            @else
                                Giao dịch không thành công. Nguyên nhân có thể do: thẻ không đủ số dư, thông tin thẻ không chính xác, hoặc bạn đã hủy giao dịch.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-between space-y-4 sm:space-y-0 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('hoc-vien.thanh-toan.form', $thanhToan->dang_ky_hoc_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Thử thanh toán lại
                </a>

                <a href="{{ route('hoc-vien.lop-hoc.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-7-7v14" />
                    </svg>
                    Quay lại danh sách lớp học
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 