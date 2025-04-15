@extends('layouts.dashboard')

@section('title', 'Chi tiết lương')
@section('page-heading', 'Chi tiết lương')

@php
    $active = 'luong';
    $role = 'giao_vien';
@endphp

@section('content')
<div class="container mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Chi tiết lương</h1>
        <a href="{{ route('giao-vien.luong.index') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin chung</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Trạng thái:</span>
                        @if($luong->trang_thai == 'cho_thanh_toan')
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ thanh toán</span>
                        @elseif($luong->trang_thai == 'da_thanh_toan')
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã thanh toán</span>
                        @endif
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Tổng tiền:</span>
                        <h4 class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($luong->tong_luong, 0, ',', '.') }} VNĐ</h4>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Vai trò:</span>
                        <p class="mt-1 capitalize">{{ $luong->vai_tro }}</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Hệ số lương:</span>
                        <p class="mt-1">{{ $luong->vaiTro->he_so_luong }}%</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Ngày thanh toán:</span>
                        <p class="mt-1">{{ $luong->ngay_thanh_toan ? $luong->ngay_thanh_toan->format('d/m/Y') : 'Chưa thanh toán' }}</p>
                    </div>
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-600">Ghi chú:</span>
                        <p class="mt-1">{{ $luong->ghi_chu ?? 'Không có' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin lớp học</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Lớp học:</span>
                                <p class="mt-1">{{ $luong->lopHoc->ten }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Khóa học:</span>
                                <p class="mt-1">{{ $luong->lopHoc->khoaHoc->ten }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Ngày bắt đầu:</span>
                                <p class="mt-1">{{ $luong->lopHoc->ngay_bat_dau->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Ngày kết thúc:</span>
                                <p class="mt-1">{{ $luong->lopHoc->ngay_ket_thuc->format('d/m/Y') }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Số học viên:</span>
                                <p class="mt-1">{{ $luong->lopHoc->hocViens->count() }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-600">Học phí gốc:</span>
                                <p class="mt-1">{{ number_format($luong->lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Danh sách học viên đã thanh toán</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thanh toán</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($luong->lopHoc->hocViens as $hocVien)
                                    @php
                                        $dangKy = $hocVien->dangKyHocs->where('lop_hoc_id', $luong->lop_hoc_id)->first();
                                    @endphp
                                    @if($dangKy && $dangKy->trang_thai_thanh_toan == 'da_thanh_toan')
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hocVien->nguoiDung->ho_ten }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hocVien->nguoiDung->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hocVien->nguoiDung->so_dien_thoai }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã thanh toán</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $dangKy->ngay_thanh_toan->format('d/m/Y') }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Lịch sử cập nhật</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày cập nhật</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái cũ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái mới</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người cập nhật</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lichSu as $ls)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ls->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ls->trang_thai_cu == 'cho_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ thanh toán</span>
                                @elseif($ls->trang_thai_cu == 'da_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã thanh toán</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ls->trang_thai_moi == 'cho_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ thanh toán</span>
                                @elseif($ls->trang_thai_moi == 'da_thanh_toan')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã thanh toán</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ls->nguoiDung->ho_ten }} ({{ ucfirst($ls->nguoiDung->vai_tro) }})</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ls->ghi_chu ?? 'Không có' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 