@extends('layouts.dashboard')

@section('title', 'Chi tiết thanh toán')
@section('page-heading', 'Chi tiết thanh toán')

@php
    $active = 'nguoi-dung';
    $role = 'admin';
@endphp
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Chi tiết thanh toán #{{ $thanhToan->ma_thanh_toan }}</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.thanh-toan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="flex items-center p-4 border-b border-gray-200">
            <div class="flex-1">
                <h2 class="text-xl font-semibold">Thông tin thanh toán</h2>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($thanhToan->trang_thai == 'da_thanh_toan' || $thanhToan->trang_thai == 'da_xac_nhan') 
                        bg-green-100 text-green-800
                    @elseif($thanhToan->trang_thai == 'cho_xac_nhan') 
                        bg-yellow-100 text-yellow-800
                    @elseif($thanhToan->trang_thai == 'da_huy') 
                        bg-red-100 text-red-800
                    @endif
                ">
                    @if($thanhToan->trang_thai == 'da_thanh_toan')
                        Đã thanh toán
                    @elseif($thanhToan->trang_thai == 'da_xac_nhan')
                        Đã xác nhận
                    @elseif($thanhToan->trang_thai == 'cho_xac_nhan')
                        Chờ xác nhận
                    @elseif($thanhToan->trang_thai == 'da_huy')
                        Đã hủy
                    @endif
                </span>
            </div>
        </div>
        <div class="p-4">
            <table class="w-full">
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600 w-1/3">Trạng thái:</td>
                        <td class="py-3 font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                @if($thanhToan->trang_thai == 'da_thanh_toan' || $thanhToan->trang_thai == 'da_xac_nhan') 
                                    bg-green-100 text-green-800
                                @elseif($thanhToan->trang_thai == 'cho_xac_nhan') 
                                    bg-yellow-100 text-yellow-800
                                @elseif($thanhToan->trang_thai == 'da_huy') 
                                    bg-red-100 text-red-800
                                @endif
                            ">
                                @if($thanhToan->trang_thai == 'da_thanh_toan')
                                    Đã thanh toán
                                @elseif($thanhToan->trang_thai == 'da_xac_nhan')
                                    Đã xác nhận
                                @elseif($thanhToan->trang_thai == 'cho_xac_nhan')
                                    Chờ xác nhận
                                @elseif($thanhToan->trang_thai == 'da_huy')
                                    Đã hủy
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Số tiền:</td>
                        <td class="py-3 font-medium">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} đ</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Phương thức thanh toán:</td>
                        <td class="py-3 font-medium">
                            @if($thanhToan->phuong_thuc == 'chuyen_khoan')
                                Chuyển khoản ngân hàng
                            @elseif($thanhToan->phuong_thuc == 'tien_mat')
                                Tiền mặt
                            @elseif($thanhToan->phuong_thuc == 'vnpay')
                                VNPay
                            @elseif($thanhToan->phuong_thuc == 'vi_dien_tu')
                                Ví điện tử
                            @else
                                {{ $thanhToan->phuong_thuc }}
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Ngày tạo:</td>
                        <td class="py-3 font-medium">{{ $thanhToan->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Ngày thanh toán:</td>
                        <td class="py-3 font-medium">
                            @if($thanhToan->ngay_thanh_toan)
                                {{ \Carbon\Carbon::parse($thanhToan->ngay_thanh_toan)->format('d/m/Y H:i:s') }}
                            @else
                                <span class="text-gray-400">Chưa thanh toán</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Mã giao dịch:</td>
                        <td class="py-3 font-medium">
                            @if($thanhToan->ma_giao_dich)
                                {{ $thanhToan->ma_giao_dich }}
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 text-gray-600">Thông tin giao dịch:</td>
                        <td class="py-3 font-medium">
                            @if($thanhToan->thong_tin_giao_dich)
                                {{ $thanhToan->thong_tin_giao_dich }}
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-gray-600">Ghi chú:</td>
                        <td class="py-3">
                            @if($thanhToan->ghi_chu)
                                <p class="whitespace-pre-line">{{ $thanhToan->ghi_chu }}</p>
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($thanhToan->trang_thai == 'cho_xac_nhan')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-green-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>Xác nhận thanh toán
                </h2>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.thanh-toan.confirm', $thanhToan->id) }}" method="POST" 
                      onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận thanh toán này?')">
                    @csrf
                    <div class="mb-4">
                        <label for="ghi_chu_xac_nhan" class="block text-gray-700 mb-2">Ghi chú xác nhận:</label>
                        <textarea id="ghi_chu_xac_nhan" name="ghi_chu_xac_nhan" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Nhập ghi chú xác nhận (nếu có)"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Xác nhận thanh toán
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-red-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-red-800">
                    <i class="fas fa-times-circle mr-2"></i>Hủy thanh toán
                </h2>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.thanh-toan.cancel', $thanhToan->id) }}" method="POST" 
                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy thanh toán này?')">
                    @csrf
                    <div class="mb-4">
                        <label for="ghi_chu_xac_nhan" class="block text-gray-700 mb-2">Lý do hủy:</label>
                        <textarea id="ghi_chu_xac_nhan" name="ghi_chu_xac_nhan" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Nhập lý do hủy thanh toán"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Hủy thanh toán
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Thông tin học viên</h2>
            </div>
            <div class="p-4">
                @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->hocVien && $thanhToan->dangKyHoc->hocVien->nguoiDung)
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex-shrink-0 mr-4 overflow-hidden">
                            @if($thanhToan->dangKyHoc->hocVien->nguoiDung->avatar)
                                <img src="{{ asset('storage/' . $thanhToan->dangKyHoc->hocVien->nguoiDung->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                                    <i class="fas fa-user text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten }}</h3>
                            <p class="text-gray-600">ID: {{ $thanhToan->dangKyHoc->hocVien->id }}</p>
                        </div>
                    </div>
                    
                    <table class="w-full">
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600 w-1/3">Email:</td>
                                <td class="py-3">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->email }}</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600">Số điện thoại:</td>
                                <td class="py-3">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->so_dien_thoai ?? 'Không có' }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-gray-600">Ngày đăng ký:</td>
                                <td class="py-3">{{ $thanhToan->dangKyHoc->created_at->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.hoc-vien.show', $thanhToan->dangKyHoc->hocVien->id) }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 inline-block">
                            Xem hồ sơ học viên
                        </a>
                    </div>
                @else
                    <p class="text-gray-500">Không tìm thấy thông tin học viên</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Thông tin lớp học</h2>
            </div>
            <div class="p-4">
                @if($thanhToan->dangKyHoc && $thanhToan->dangKyHoc->lopHoc)
                    <table class="w-full">
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600 w-1/3">Tên lớp:</td>
                                <td class="py-3 font-medium">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600">Mã lớp:</td>
                                <td class="py-3">{{ $thanhToan->dangKyHoc->lopHoc->ma_lop }}</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600">Khóa học:</td>
                                <td class="py-3">
                                    @if($thanhToan->dangKyHoc->lopHoc->khoaHoc)
                                        {{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}
                                    @else
                                        <span class="text-gray-400">Không có</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600">Học phí:</td>
                                <td class="py-3">{{ number_format($thanhToan->dangKyHoc->lopHoc->hoc_phi, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 text-gray-600">Giáo viên:</td>
                                <td class="py-3">
                                    @if($thanhToan->dangKyHoc->lopHoc->giaoVien && $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung)
                                        {{ $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung->ho_ten }}
                                    @else
                                        <span class="text-gray-400">Chưa phân công</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 text-gray-600">Trạng thái lớp:</td>
                                <td class="py-3">
                                    @if($thanhToan->dangKyHoc->lopHoc->trang_thai == 'dang_dien_ra')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                            Đang diễn ra
                                        </span>
                                    @elseif($thanhToan->dangKyHoc->lopHoc->trang_thai == 'sap_dien_ra')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            Sắp diễn ra
                                        </span>
                                    @elseif($thanhToan->dangKyHoc->lopHoc->trang_thai == 'da_ket_thuc')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                            Đã kết thúc
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                            {{ $thanhToan->dangKyHoc->lopHoc->trang_thai }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.lop-hoc.show', $thanhToan->dangKyHoc->lopHoc->id) }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 inline-block">
                            Xem chi tiết lớp học
                        </a>
                    </div>
                @else
                    <p class="text-gray-500">Không tìm thấy thông tin lớp học</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Cập nhật ghi chú</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.thanh-toan.update', $thanhToan->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="ghi_chu" class="block text-gray-700 mb-2">Ghi chú:</label>
                    <textarea id="ghi_chu" name="ghi_chu" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $thanhToan->ghi_chu }}</textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Cập nhật ghi chú
                </button>
            </form>
        </div>
    </div>

    @if($thanhToan->phuong_thuc == 'vnpay' && $thanhToan->responseData)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Thông tin giao dịch VNPay</h2>
            </div>
            <div class="p-4">
                @php
                    $responseData = json_decode($thanhToan->responseData, true);
                @endphp
                
                @if($responseData && is_array($responseData))
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody>
                                @foreach($responseData as $key => $value)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-2 text-gray-600 w-1/3">{{ $key }}:</td>
                                        <td class="py-2">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Không có dữ liệu phản hồi từ VNPay</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection 