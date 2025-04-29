@extends('layouts.dashboard')

@section('title', 'Hóa đơn')
@section('page-heading', 'Hóa đơn')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6">
        <a href="{{ route('hoc-vien.tai-chinh.thanh-toan-thanh-cong', $hoaDon->thanhToan->ma_thanh_toan) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
            </svg>
            Quay lại
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg max-w-4xl mx-auto">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">HÓA ĐƠN ĐIỆN TỬ</h3>
                <p class="max-w-2xl mt-1 text-sm text-gray-500">Mã hóa đơn: {{ $hoaDon->ma_hoa_don }}</p>
            </div>
            <div class="print:hidden">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    In hóa đơn
                </button>
            </div>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-700 mb-4">Thông tin bên bán:</div>
                    <h4 class="text-base font-bold">CÔNG TY TNHH HANZII EDUCATION</h4>
                    <p class="text-sm text-gray-600">Địa chỉ: 123 Đường ABC, Quận X, TP. HCM</p>
                    <p class="text-sm text-gray-600">Mã số thuế: 0123456789</p>
                    <p class="text-sm text-gray-600">Điện thoại: 0123.456.789</p>
                    <p class="text-sm text-gray-600">Email: info@hanzii.com</p>
                </div>
                
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-700 mb-4">Thông tin hóa đơn:</div>
                    <p class="text-sm text-gray-600">Ngày lập: {{ \Carbon\Carbon::parse($hoaDon->ngay_tao)->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-600">Phương thức thanh toán: VNPay</p>
                    <p class="text-sm text-gray-600">Mã giao dịch: {{ $hoaDon->thanhToan->ma_giao_dich }}</p>
                </div>
            </div>
            
            <div class="mt-8">
                <div class="text-sm font-medium text-gray-700 mb-4">Thông tin khách hàng:</div>
                <p class="text-sm text-gray-600">Họ tên: {{ auth()->user()->ho ?? '' }} {{ auth()->user()->ten ?? '' }}</p>
                <p class="text-sm text-gray-600">Email: {{ auth()->user()->email ?? '' }}</p>
                <p class="text-sm text-gray-600">Số điện thoại: {{ auth()->user()->so_dien_thoai ?? '' }}</p>
            </div>
            
            <div class="mt-8">
                <div class="text-sm font-medium text-gray-700 mb-4">Chi tiết hóa đơn:</div>
                
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                STT
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dịch vụ
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Số lượng
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Đơn giá
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Thành tiền
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                1
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Học phí lớp {{ $hoaDon->lopHoc->ten }} (Khóa học: {{ $hoaDon->lopHoc->khoaHoc->ten }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                1
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($hoaDon->tong_tien, 0, ',', '.') }} VNĐ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($hoaDon->tong_tien, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <div class="w-1/2">
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between py-2 text-sm text-gray-500">
                            <dt>Tổng tiền dịch vụ</dt>
                            <dd>{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} VNĐ</dd>
                        </div>
                        
                        <div class="flex justify-between py-2 text-sm text-gray-500">
                            <dt>VAT (0%)</dt>
                            <dd>0 VNĐ</dd>
                        </div>
                        
                        <div class="flex justify-between py-2 text-sm font-medium">
                            <dt>Tổng thanh toán</dt>
                            <dd class="text-red-600 font-bold">{{ number_format($hoaDon->tong_tien, 0, ',', '.') }} VNĐ</dd>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 border-t border-gray-200 pt-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="text-sm font-medium text-gray-700 mb-4">Ghi chú:</div>
                        <p class="text-sm text-gray-600">{{ $hoaDon->ghi_chu ?? 'Không có ghi chú' }}</p>
                    </div>
                    
                    <div>
                        <div class="text-sm font-medium text-gray-700 mb-4">Chữ ký điện tử:</div>
                        <p class="text-sm text-gray-600">Hóa đơn này được tạo tự động và có giá trị pháp lý mà không cần chữ ký.</p>
                        <p class="text-sm text-gray-600">Mã xác thực: {{ md5($hoaDon->ma_hoa_don . $hoaDon->ngay_tao) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-6 print:hidden">
        <a href="{{ route('hoc-vien.tai-chinh.index') }}" class="text-red-600 hover:text-red-800">
            Quay lại quản lý tài chính
        </a>
    </div>
    
    <style type="text/css" media="print">
        @page { size: auto; margin: 10mm; }
        body { margin: 0; }
        .print\:hidden { display: none !important; }
    </style>
@endsection 