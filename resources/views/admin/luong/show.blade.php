@extends('layouts.dashboard')

@section('title', 'Chi tiết lương')
@section('page-heading', 'Chi tiết lương')

@php
    $active = 'luong';
    $role = 'admin';
@endphp

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.luong.index') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách lương
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin lương</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thông tin khóa học và lớp học</h3>
                                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                    <p class="mb-2"><span class="font-medium">Khóa học:</span> {{ $luong->lopHoc->khoaHoc->ten }}</p>
                                    <p class="mb-2"><span class="font-medium">Lớp học:</span> {{ $luong->lopHoc->ten }} ({{ $luong->lopHoc->ma }})</p>
                                    <p class="mb-2"><span class="font-medium">Tổng số học viên:</span> {{ $luong->lopHoc->hocViens->count() }}</p>
                                    <p class="mb-2"><span class="font-medium">Tổng học phí:</span> {{ number_format($luong->lopHoc->khoaHoc->hoc_phi * $luong->lopHoc->hocViens->count(), 0, ',', '.') }} đ</p>
                                    <p><span class="font-medium">Thời gian kết thúc:</span> {{ $luong->lopHoc->ngay_ket_thuc->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Thông tin người nhận lương</h3>
                                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                    <p class="mb-2"><span class="font-medium">Họ tên:</span> {{ $luong->nguoiDung->ho_ten }}</p>
                                    <p class="mb-2"><span class="font-medium">Email:</span> {{ $luong->nguoiDung->email }}</p>
                                    <p class="mb-2"><span class="font-medium">Số điện thoại:</span> {{ $luong->nguoiDung->so_dien_thoai }}</p>
                                    <p><span class="font-medium">Vai trò:</span> {{ $luong->vai_tro == 'giao_vien' ? 'Giáo viên' : 'Trợ giảng' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thông tin thanh toán</h3>
                                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                    <p class="mb-2"><span class="font-medium">Phần trăm lương:</span> {{ $luong->phan_tram }}%</p>
                                    <p class="mb-2"><span class="font-medium">Số tiền lương:</span> <span class="text-lg font-semibold text-blue-600">{{ number_format($luong->so_tien, 0, ',', '.') }} đ</span></p>
                                    <p class="mb-2"><span class="font-medium">Trạng thái:</span> 
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $luong->trang_thai == 'da_thanh_toan' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $luong->trang_thai == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                        </span>
                                    </p>
                                    <p class="mb-2"><span class="font-medium">Ngày tạo:</span> {{ $luong->created_at->format('d/m/Y H:i') }}</p>
                                    @if($luong->trang_thai == 'da_thanh_toan')
                                        <p><span class="font-medium">Ngày thanh toán:</span> {{ $luong->ngay_thanh_toan->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Ghi chú</h3>
                                <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                    @if($luong->ghi_chu)
                                        <p>{{ $luong->ghi_chu }}</p>
                                    @else
                                        <p class="text-gray-400 italic">Không có ghi chú</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($luong->trang_thai != 'da_thanh_toan')
                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('admin.luong.edit', $luong->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                                    </a>
                                    <form action="{{ route('admin.luong.thanh-toan', $luong->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="return confirm('Bạn có chắc chắn muốn đánh dấu lương này là đã thanh toán?')">
                                            <i class="fas fa-money-bill-wave mr-2"></i> Đánh dấu đã thanh toán
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 