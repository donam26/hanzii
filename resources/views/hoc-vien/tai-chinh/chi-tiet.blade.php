@extends('layouts.dashboard')

@section('title', 'Chi tiết giao dịch')
@section('page-heading', 'Chi tiết giao dịch')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Chi tiết giao dịch</h2>
        
        <div class="flex space-x-2">
            <a href="{{ route('hoc-vien.tai-chinh.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Chi tiết giao dịch #{{ $transaction->ma_giao_dich }}
            </h3>
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                {{ $transaction->trang_thai === 'thanh_cong' ? 'bg-green-100 text-green-800' : 
                    ($transaction->trang_thai === 'dang_xu_ly' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                {{ $transaction->trang_thai === 'thanh_cong' ? 'Thành công' : 
                    ($transaction->trang_thai === 'dang_xu_ly' ? 'Đang xử lý' : 'Thất bại') }}
            </span>
        </div>
        
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Thời gian giao dịch
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $transaction->created_at->format('H:i:s d/m/Y') }}
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Loại giao dịch
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($transaction->loai_giao_dich === 'nap_tien')
                            Nạp tiền
                        @elseif($transaction->loai_giao_dich === 'thanh_toan')
                            Thanh toán
                        @else
                            {{ $transaction->loai_giao_dich }}
                        @endif
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Phương thức thanh toán
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($transaction->phuong_thuc === 'banking')
                            Chuyển khoản ngân hàng
                        @elseif($transaction->phuong_thuc === 'momo')
                            Ví MoMo
                        @elseif($transaction->phuong_thuc === 'zalopay')
                            Ví ZaloPay
                        @elseif($transaction->phuong_thuc === 'tien_mat')
                            Tiền mặt
                        @else
                            {{ $transaction->phuong_thuc }}
                        @endif
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Số tiền
                    </dt>
                    <dd class="mt-1 text-sm font-medium {{ $transaction->loai_giao_dich === 'nap_tien' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->loai_giao_dich === 'nap_tien' ? '+' : '-' }}{{ number_format($transaction->so_tien, 0, ',', '.') }} VNĐ
                    </dd>
                </div>
                
                @if($transaction->dich_vu_id)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Dịch vụ
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $transaction->dich_vu->ten_dich_vu ?? 'Không xác định' }}
                    </dd>
                </div>
                @endif
                
                @if($transaction->lop_hoc_id)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Lớp học
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $transaction->lop_hoc->ten_lop ?? 'Không xác định' }}
                    </dd>
                </div>
                @endif
                
                @if($transaction->ghi_chu)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Ghi chú
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $transaction->ghi_chu }}
                    </dd>
                </div>
                @endif
                
                @if($transaction->thong_tin_giao_dich)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        Thông tin thêm
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-md overflow-auto max-h-40">
                        <pre class="text-xs">{{ $transaction->thong_tin_giao_dich }}</pre>
                    </dd>
                </div>
                @endif
            </dl>
        </div>
        
        @if($transaction->trang_thai === 'dang_xu_ly' && $transaction->loai_giao_dich === 'nap_tien')
        <div class="px-6 py-3 bg-gray-50 text-right">
            <button id="openHuyGiaoDichBtn" type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Hủy giao dịch
            </button>
        </div>
        @endif
    </div>
    
    @if($transaction->loai_giao_dich === 'nap_tien' && $transaction->phuong_thuc === 'banking' && $transaction->trang_thai === 'dang_xu_ly')
    <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Thông tin chuyển khoản
            </h3>
        </div>
        
        <div class="px-6 py-5 bg-yellow-50">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Giao dịch của bạn đang được xử lý
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Vui lòng chuyển khoản với các thông tin sau để hoàn tất giao dịch:</p>
                        <ul class="list-disc pl-5 space-y-1 mt-2">
                            <li>Ngân hàng: <span class="font-medium">BIDV</span></li>
                            <li>Số tài khoản: <span class="font-medium">12345678900</span></li>
                            <li>Tên tài khoản: <span class="font-medium">CÔNG TY HANZII</span></li>
                            <li>Số tiền: <span class="font-medium">{{ number_format($transaction->so_tien, 0, ',', '.') }} VNĐ</span></li>
                            <li>Nội dung chuyển khoản: <span class="font-medium">HANZII {{ $transaction->ma_giao_dich }}</span></li>
                        </ul>
                        <p class="mt-2">Lưu ý: Giao dịch sẽ được xác nhận sau khi chúng tôi nhận được tiền (thường trong vòng 24 giờ làm việc).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Modal xác nhận hủy giao dịch -->
    <div id="huyGiaoDichModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('hoc-vien.tai-chinh.huy-giao-dich', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Xác nhận hủy giao dịch
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Bạn có chắc chắn muốn hủy giao dịch này không? Hành động này không thể hoàn tác.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xác nhận hủy
                        </button>
                        <button type="button" id="closeHuyGiaoDichModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Đóng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const huyGiaoDichModal = document.getElementById('huyGiaoDichModal');
            const openHuyGiaoDichBtn = document.getElementById('openHuyGiaoDichBtn');
            const closeHuyGiaoDichModal = document.getElementById('closeHuyGiaoDichModal');
            
            if (openHuyGiaoDichBtn) {
                openHuyGiaoDichBtn.addEventListener('click', function() {
                    huyGiaoDichModal.classList.remove('hidden');
                });
            }
            
            if (closeHuyGiaoDichModal) {
                closeHuyGiaoDichModal.addEventListener('click', function() {
                    huyGiaoDichModal.classList.add('hidden');
                });
            }
        });
    </script>
    @endpush
@endsection 