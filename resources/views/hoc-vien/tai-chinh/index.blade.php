@extends('layouts.dashboard')

@section('title', 'Quản lý tài chính')
@section('page-heading', 'Quản lý tài chính')

@php
    $active = 'tai-chinh';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách giao dịch</h2>
        
        <div class="flex space-x-2">
            <a href="{{ route('hoc-vien.tai-chinh.thong-ke') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Xem thống kê
            </a>
            <a href="{{ route('hoc-vien.tai-chinh.lop-chua-dong-tien') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lớp chưa đóng tiền
            </a>
            <a href="#" id="openNapTienModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nạp tiền
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-4 shadow-md rounded-lg mb-6">
        <form action="{{ route('hoc-vien.tai-chinh.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="loai_giao_dich" class="block text-sm font-medium text-gray-700 mb-1">Loại giao dịch</label>
                <select id="loai_giao_dich" name="loai_giao_dich" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    <option value="">Tất cả</option>
                    <option value="nap_tien" {{ request('loai_giao_dich') == 'nap_tien' ? 'selected' : '' }}>Nạp tiền</option>
                    <option value="thanh_toan" {{ request('loai_giao_dich') == 'thanh_toan' ? 'selected' : '' }}>Thanh toán</option>
                </select>
            </div>
            
            <div>
                <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    <option value="">Tất cả</option>
                    <option value="thanh_cong" {{ request('trang_thai') == 'thanh_cong' ? 'selected' : '' }}>Thành công</option>
                    <option value="dang_xu_ly" {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="that_bai" {{ request('trang_thai') == 'that_bai' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>
            
            <div>
                <label for="tu_ngay" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" id="tu_ngay" name="tu_ngay" value="{{ request('tu_ngay') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="den_ngay" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" id="den_ngay" name="den_ngay" value="{{ request('den_ngay') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
            </div>
            
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã giao dịch
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thời gian
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Loại
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số tiền
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thông tin
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $transaction->ma_giao_dich }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at->format('H:i:s d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($transaction->loai_giao_dich === 'nap_tien')
                                Nạp tiền
                            @elseif($transaction->loai_giao_dich === 'thanh_toan')
                                Thanh toán
                            @else
                                {{ $transaction->loai_giao_dich }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->loai_giao_dich === 'nap_tien' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->loai_giao_dich === 'nap_tien' ? '+' : '-' }}{{ number_format($transaction->so_tien, 0, ',', '.') }} VNĐ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->trang_thai === 'thanh_cong' ? 'bg-green-100 text-green-800' : 
                               ($transaction->trang_thai === 'dang_xu_ly' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $transaction->trang_thai === 'thanh_cong' ? 'Thành công' : 
                                  ($transaction->trang_thai === 'dang_xu_ly' ? 'Đang xử lý' : 'Thất bại') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                            @if($transaction->dich_vu)
                                {{ $transaction->dich_vu->ten_dich_vu }}
                            @elseif($transaction->lop_hoc)
                                {{ $transaction->lop_hoc->ten_lop }}
                            @else
                                {{ $transaction->mo_ta ?: 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('hoc-vien.tai-chinh.chi-tiet', $transaction->id) }}" class="text-red-600 hover:text-red-900">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Không có giao dịch nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Nạp tiền -->
    <div id="napTienModal" class="fixed inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('hoc-vien.tai-chinh.nap-tien') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Nạp tiền vào tài khoản
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="so_tien" class="block text-sm font-medium text-gray-700">Số tiền (VNĐ)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="so_tien" id="so_tien" class="focus:ring-red-500 focus:border-red-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0" min="10000" step="10000" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">
                                                    VNĐ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="phuong_thuc" class="block text-sm font-medium text-gray-700">Phương thức thanh toán</label>
                                        <select id="phuong_thuc" name="phuong_thuc" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md" required>
                                            <option value="banking">Chuyển khoản ngân hàng</option>
                                            <option value="momo">Ví MoMo</option>
                                            <option value="zalopay">Ví ZaloPay</option>
                                        </select>
                                    </div>
                                    
                                    <div id="bankingInfo" class="bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-900">Thông tin chuyển khoản:</h4>
                                        <p class="mt-1 text-sm text-gray-500">Ngân hàng: <span class="font-medium">BIDV</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Số tài khoản: <span class="font-medium">12345678900</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Chủ tài khoản: <span class="font-medium">CÔNG TY HANZII</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Nội dung chuyển khoản: <span class="font-medium">HANZII [Họ và tên]</span></p>
                                    </div>
                                    
                                    <div id="momoInfo" class="hidden bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-900">Thông tin chuyển tiền MoMo:</h4>
                                        <p class="mt-1 text-sm text-gray-500">Số điện thoại: <span class="font-medium">0987654321</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Tên tài khoản: <span class="font-medium">CÔNG TY HANZII</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Nội dung chuyển tiền: <span class="font-medium">HANZII [Họ và tên]</span></p>
                                    </div>
                                    
                                    <div id="zalopayInfo" class="hidden bg-gray-50 p-4 rounded-md">
                                        <h4 class="text-sm font-medium text-gray-900">Thông tin chuyển tiền ZaloPay:</h4>
                                        <p class="mt-1 text-sm text-gray-500">Số điện thoại: <span class="font-medium">0987654321</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Tên tài khoản: <span class="font-medium">CÔNG TY HANZII</span></p>
                                        <p class="mt-1 text-sm text-gray-500">Nội dung chuyển tiền: <span class="font-medium">HANZII [Họ và tên]</span></p>
                                    </div>
                                    
                                    <div>
                                        <label for="ghi_chu" class="block text-sm font-medium text-gray-700">Ghi chú (tùy chọn)</label>
                                        <textarea id="ghi_chu" name="ghi_chu" rows="2" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Xác nhận nạp tiền
                        </button>
                        <button type="button" id="closeNapTienModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const napTienModal = document.getElementById('napTienModal');
            const openNapTienModal = document.getElementById('openNapTienModal');
            const closeNapTienModal = document.getElementById('closeNapTienModal');
            const phuongThucSelect = document.getElementById('phuong_thuc');
            const bankingInfo = document.getElementById('bankingInfo');
            const momoInfo = document.getElementById('momoInfo');
            const zalopayInfo = document.getElementById('zalopayInfo');
            
            openNapTienModal.addEventListener('click', function() {
                napTienModal.classList.remove('hidden');
            });
            
            closeNapTienModal.addEventListener('click', function() {
                napTienModal.classList.add('hidden');
            });
            
            phuongThucSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                
                bankingInfo.classList.add('hidden');
                momoInfo.classList.add('hidden');
                zalopayInfo.classList.add('hidden');
                
                if (selectedValue === 'banking') {
                    bankingInfo.classList.remove('hidden');
                } else if (selectedValue === 'momo') {
                    momoInfo.classList.remove('hidden');
                } else if (selectedValue === 'zalopay') {
                    zalopayInfo.classList.remove('hidden');
                }
            });
        });
    </script>
    @endpush
@endsection 