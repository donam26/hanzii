@extends('layouts.dashboard')

@section('title', 'Thanh toán thành công')
@section('page-heading', 'Thanh toán thành công')

@php
    $active = 'khoa-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-green-100 text-green-600 text-3xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Thanh toán thành công!</h2>
        <p class="text-gray-600 mb-8">Cảm ơn bạn đã thanh toán học phí. Bạn đã đăng ký thành công lớp học.</p>
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
                            <dt class="text-sm font-medium text-gray-500">Mã giao dịch</dt>
                            <dd class="text-base text-gray-900">{{ $thanhToan->ma_giao_dich ?? 'N/A' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thời gian thanh toán</dt>
                            <dd class="text-base text-gray-900">{{ \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('d/m/Y H:i:s') }}</dd>
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
                            <dd class="text-lg font-semibold text-green-600">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đã thanh toán
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-between space-y-4 sm:space-y-0 mt-8 pt-6 border-t border-gray-200">
                @if($thanhToan->hoaDon)
                <a href="{{ route('hoc-vien.thanh-toan.hoa-don', $thanhToan->hoaDon->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Xem hóa đơn
                </a>
                @endif

                <a href="{{ route('hoc-vien.lop-hoc.show', $thanhToan->dangKyHoc->lop_hoc_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    Đi đến lớp học
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 