@extends('layouts.dashboard')

@section('title', 'Quản lý thanh toán')
@section('page-heading', 'Quản lý thanh toán')

@php
    $active = 'thanh-toan';
    $role = 'admin';
@endphp

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Thống kê thanh toán -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng số thanh toán</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tongThanhToan) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-receipt text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng số thanh toán tháng này</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tongThanhToanThang) }}</p>
                </div>
                <div class="bg-green-100 rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng số tiền thanh toán</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tongSoTien, 0, ',', '.') }} đ</p>
                </div>
                <div class="bg-red-100 rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-red-500 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng tiền tháng này</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($tongSoTienThang, 0, ',', '.') }} đ</p>
                </div>
                <div class="bg-indigo-100 rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-chart-line text-indigo-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tìm kiếm và lọc -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Tìm kiếm và lọc</h2>
        <form action="{{ route('admin.thanh-toan.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" placeholder="Tên học viên, mã lớp...">
                </div>
                
                <div>
                    <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="trang_thai" id="trang_thai" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <option value="">Tất cả trạng thái</option>
                        <option value="cho_xac_nhan" {{ request('trang_thai') == 'cho_xac_nhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                
                <div>
                    <label for="phuong_thuc" class="block text-sm font-medium text-gray-700 mb-1">Phương thức</label>
                    <select name="phuong_thuc" id="phuong_thuc" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <option value="">Tất cả phương thức</option>
                        <option value="chuyen_khoan" {{ request('phuong_thuc') == 'chuyen_khoan' ? 'selected' : '' }}>Chuyển khoản</option>
                        <option value="tien_mat" {{ request('phuong_thuc') == 'tien_mat' ? 'selected' : '' }}>Tiền mặt</option>
                        <option value="vi_dien_tu" {{ request('phuong_thuc') == 'vi_dien_tu' ? 'selected' : '' }}>Ví điện tử</option>
                    </select>
                </div>
                
                <div>
                    <label for="tu_ngay" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" name="tu_ngay" id="tu_ngay" value="{{ request('tu_ngay') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="den_ngay" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="den_ngay" id="den_ngay" value="{{ request('den_ngay') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-search mr-2"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.thanh-toan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-sync-alt mr-2"></i> Đặt lại
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Danh sách thanh toán -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Danh sách thanh toán</h2>
            <a href="{{ route('admin.thanh-toan.index', ['export' => true]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-export mr-2"></i> Xuất báo cáo
            </a>
        </div>
        
        @if($thanhToans->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã thanh toán</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Học viên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thanh toán</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($thanhToans as $thanhToan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $thanhToan->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $thanhToan->dangKyHoc->hocVien->nguoiDung->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $thanhToan->dangKyHoc->lopHoc->ma_lop }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ number_format($thanhToan->so_tien, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($thanhToan->phuong_thuc_thanh_toan == 'chuyen_khoan')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-university mr-1"></i> Chuyển khoản
                                        </span>
                                    @elseif($thanhToan->phuong_thuc_thanh_toan == 'tien_mat')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-money-bill-alt mr-1"></i> Tiền mặt
                                        </span>
                                    @elseif($thanhToan->phuong_thuc_thanh_toan == 'vi_dien_tu')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-wallet mr-1"></i> Ví điện tử
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-credit-card mr-1"></i> {{ $thanhToan->phuong_thuc_thanh_toan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $thanhToan->ngay_thanh_toan ? \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('d/m/Y') : 'Chưa thanh toán' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Chờ xác nhận
                                        </span>
                                    @elseif($thanhToan->trang_thai == 'da_thanh_toan')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Đã thanh toán
                                        </span>
                                    @elseif($thanhToan->trang_thai == 'da_huy')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Đã hủy
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $thanhToan->trang_thai }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('admin.thanh-toan.show', $thanhToan->id) }}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.thanh-toan.edit', $thanhToan->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Cập nhật">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                            <form action="{{ route('admin.thanh-toan.update', $thanhToan->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="trang_thai" value="da_thanh_toan">
                                                <input type="hidden" name="ngay_thanh_toan" value="{{ now()->format('Y-m-d') }}">
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Xác nhận thanh toán" onclick="return confirm('Xác nhận thanh toán này?')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $thanhToans->withQueryString()->links() }}
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có thanh toán nào</h3>
                <p class="mt-1 text-sm text-gray-500">Chưa có dữ liệu thanh toán nào được tìm thấy.</p>
            </div>
        @endif
    </div>
    
    <!-- Thống kê theo phương thức thanh toán -->
    @if($thongKeTheoPhuongThuc->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Thống kê theo phương thức thanh toán</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($thongKeTheoPhuongThuc as $thongKe)
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($thongKe->phuong_thuc_thanh_toan == 'chuyen_khoan')
                            <div class="flex items-center text-blue-600 mb-2">
                                <i class="fas fa-university text-xl mr-2"></i>
                                <h3 class="font-semibold">Chuyển khoản</h3>
                            </div>
                        @elseif($thongKe->phuong_thuc_thanh_toan == 'tien_mat')
                            <div class="flex items-center text-green-600 mb-2">
                                <i class="fas fa-money-bill-alt text-xl mr-2"></i>
                                <h3 class="font-semibold">Tiền mặt</h3>
                            </div>
                        @elseif($thongKe->phuong_thuc_thanh_toan == 'vi_dien_tu')
                            <div class="flex items-center text-purple-600 mb-2">
                                <i class="fas fa-wallet text-xl mr-2"></i>
                                <h3 class="font-semibold">Ví điện tử</h3>
                            </div>
                        @else
                            <div class="flex items-center text-gray-600 mb-2">
                                <i class="fas fa-credit-card text-xl mr-2"></i>
                                <h3 class="font-semibold">{{ $thongKe->phuong_thuc_thanh_toan }}</h3>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-gray-500 text-sm">Số lượng:</p>
                                <p class="text-gray-800 font-bold">{{ number_format($thongKe->so_luong) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Tổng tiền:</p>
                                <p class="text-gray-800 font-bold">{{ number_format($thongKe->tong_tien, 0, ',', '.') }} đ</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection 