@extends('layouts.dashboard')

@section('title', 'Quản lý thanh toán')
@section('page-heading', 'Quản lý thanh toán')

@php
    $active = 'nguoi-dung';
    $role = 'admin';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Quản lý thanh toán</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.thanh-toan.thong-ke-ngay') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-calendar-day mr-2"></i>Thống kê theo ngày
            </a>
            <a href="{{ route('admin.thanh-toan.thong-ke-thang') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-calendar-alt mr-2"></i>Thống kê theo tháng
            </a>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-blue-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-blue-800">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Tổng số thanh toán
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-blue-600">{{ number_format($tongThanhToan) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-green-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-money-bill-wave mr-2"></i>Tổng tiền đã thanh toán
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-green-600">{{ number_format($tongSoTien, 0, ',', '.') }} đ</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-purple-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-purple-800">
                    <i class="fas fa-calendar-check mr-2"></i>Thanh toán tháng này
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-purple-600">{{ number_format($thanToanthThangNay) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-yellow-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-yellow-800">
                    <i class="fas fa-coins mr-2"></i>Tiền thu tháng này
                </h2>
            </div>
            <div class="p-6 flex justify-center items-center">
                <span class="text-4xl font-bold text-yellow-600">{{ number_format($tongTienThangNay, 0, ',', '.') }} đ</span>
            </div>
        </div>
    </div>

    <!-- Tìm kiếm và lọc -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Tìm kiếm và lọc thanh toán</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.thanh-toan.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-gray-700 mb-2">Tìm kiếm:</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Tên học viên, mã thanh toán, email...">
                    </div>

                    <div>
                        <label for="trang_thai" class="block text-gray-700 mb-2">Trạng thái:</label>
                        <select id="trang_thai" name="trang_thai" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="cho_xac_nhan" {{ request('trang_thai') == 'cho_xac_nhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="da_xac_nhan" {{ request('trang_thai') == 'da_xac_nhan' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>

                    <div>
                        <label for="phuong_thuc" class="block text-gray-700 mb-2">Phương thức:</label>
                        <select id="phuong_thuc" name="phuong_thuc" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tất cả phương thức --</option>
                            <option value="chuyen_khoan" {{ request('phuong_thuc') == 'chuyen_khoan' ? 'selected' : '' }}>Chuyển khoản</option>
                            <option value="tien_mat" {{ request('phuong_thuc') == 'tien_mat' ? 'selected' : '' }}>Tiền mặt</option>
                            <option value="vnpay" {{ request('phuong_thuc') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                            <option value="vi_dien_tu" {{ request('phuong_thuc') == 'vi_dien_tu' ? 'selected' : '' }}>Ví điện tử</option>
                        </select>
                    </div>

                    <div>
                        <label for="hoc_vien" class="block text-gray-700 mb-2">Học viên:</label>
                        <input type="text" id="hoc_vien" name="hoc_vien" value="{{ request('hoc_vien') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Tên hoặc email học viên">
                    </div>

                    <div>
                        <label for="lop_hoc" class="block text-gray-700 mb-2">Lớp học:</label>
                        <input type="text" id="lop_hoc" name="lop_hoc" value="{{ request('lop_hoc') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Tên lớp học">
                    </div>

                    <div>
                        <label for="khoa_hoc" class="block text-gray-700 mb-2">Khóa học:</label>
                        <input type="text" id="khoa_hoc" name="khoa_hoc" value="{{ request('khoa_hoc') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Tên khóa học">
                    </div>

                    <div>
                        <label for="tu_ngay" class="block text-gray-700 mb-2">Từ ngày:</label>
                        <input type="date" id="tu_ngay" name="tu_ngay" value="{{ request('tu_ngay') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="den_ngay" class="block text-gray-700 mb-2">Đến ngày:</label>
                        <input type="date" id="den_ngay" name="den_ngay" value="{{ request('den_ngay') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                <i class="fas fa-search mr-2"></i>Tìm kiếm
                            </button>
                            <a href="{{ route('admin.thanh-toan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                <i class="fas fa-sync-alt mr-2"></i>Đặt lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách thanh toán -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Danh sách thanh toán</h2>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã thanh toán
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Học viên
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lớp học
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số tiền
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Phương thức
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày thanh toán
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($danhSachThanhToan as $thanhToan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $thanhToan->ma_thanh_toan ?? 'TT-' . $thanhToan->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->hocVien && $thanhToan->dangKyHoc->hocVien->nguoiDung)
                                        <div class="text-sm font-medium text-gray-900">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten }}</div>
                                        <div class="text-xs text-gray-500">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->email }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">Không có dữ liệu</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->lopHoc)
                                        <div class="text-sm font-medium text-gray-900">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</div>
                                        @if($thanhToan->dangKyHoc->lopHoc->khoaHoc)
                                            <div class="text-xs text-gray-500">{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</div>
                                        @endif
                                    @else
                                        <div class="text-sm text-gray-500">Không có dữ liệu</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($thanhToan->phuong_thuc == 'chuyen_khoan') bg-blue-100 text-blue-800
                                        @elseif($thanhToan->phuong_thuc == 'tien_mat') bg-green-100 text-green-800
                                        @elseif($thanhToan->phuong_thuc == 'vnpay') bg-yellow-100 text-yellow-800
                                        @elseif($thanhToan->phuong_thuc == 'vi_dien_tu') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($thanhToan->phuong_thuc == 'chuyen_khoan')
                                            Chuyển khoản
                                        @elseif($thanhToan->phuong_thuc == 'tien_mat')
                                            Tiền mặt
                                        @elseif($thanhToan->phuong_thuc == 'vnpay')
                                            VNPay
                                        @elseif($thanhToan->phuong_thuc == 'vi_dien_tu')
                                            Ví điện tử
                                        @else
                                            {{ $thanhToan->phuong_thuc }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($thanhToan->ngay_thanh_toan)
                                            {{ \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($thanhToan->trang_thai == 'da_thanh_toan' || $thanhToan->trang_thai == 'da_xac_nhan') bg-green-100 text-green-800
                                        @elseif($thanhToan->trang_thai == 'cho_xac_nhan') bg-yellow-100 text-yellow-800
                                        @elseif($thanhToan->trang_thai == 'da_huy') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($thanhToan->trang_thai == 'da_thanh_toan')
                                            Đã thanh toán
                                        @elseif($thanhToan->trang_thai == 'da_xac_nhan')
                                            Đã xác nhận
                                        @elseif($thanhToan->trang_thai == 'cho_xac_nhan')
                                            Chờ xác nhận
                                        @elseif($thanhToan->trang_thai == 'da_huy')
                                            Đã hủy
                                        @else
                                            {{ $thanhToan->trang_thai }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.thanh-toan.show', $thanhToan->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                            <form action="{{ route('admin.thanh-toan.confirm', $thanhToan->id) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận thanh toán này?')">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.thanh-toan.cancel', $thanhToan->id) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn hủy thanh toán này?')">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Không có dữ liệu thanh toán
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="mt-4">
                {{ $danhSachThanhToan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 