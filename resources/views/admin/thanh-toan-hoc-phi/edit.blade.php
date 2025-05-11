@extends('layouts.dashboard')

@section('title', 'Quản lý thanh toán học phí')
@section('page-heading', 'Quản lý thanh toán học phí')

@php
    $active = 'thanh-toan-hoc-phi';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Chỉnh sửa thanh toán học phí</h3>
            <a href="{{ route('admin.thanh-toan-hoc-phi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.thanh-toan-hoc-phi.update', $thanhToanHocPhi->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div>
                    <label for="ma_thanh_toan" class="block text-sm font-medium text-gray-700">Mã thanh toán</label>
                    <input type="text" id="ma_thanh_toan" name="ma_thanh_toan" value="{{ $thanhToanHocPhi->ma_thanh_toan }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hoc_vien_id" class="block text-sm font-medium text-gray-700">Học viên</label>
                        <select id="hoc_vien_id" name="hoc_vien_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">-- Chọn học viên --</option>
                            @foreach($hocViens as $hocVien)
                            <option value="{{ $hocVien->id }}" {{ $thanhToanHocPhi->hoc_vien_id == $hocVien->id ? 'selected' : '' }}>
                                {{ $hocVien->nguoiDung ? $hocVien->nguoiDung->ho . ' ' . $hocVien->nguoiDung->ten : 'Học viên #' . $hocVien->id }} 
                                ({{ $hocVien->nguoiDung ? $hocVien->nguoiDung->email : 'Không có email' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700">Lớp học</label>
                        <select id="lop_hoc_id" name="lop_hoc_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="">-- Chọn lớp học --</option>
                            @foreach($lopHocs as $lopHoc)
                            <option value="{{ $lopHoc->id }}" {{ $thanhToanHocPhi->lop_hoc_id == $lopHoc->id ? 'selected' : '' }}>
                                {{ $lopHoc->ten }} ({{ $lopHoc->ma_lop }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="so_tien" class="block text-sm font-medium text-gray-700">Số tiền (VND)</label>
                        <input type="number" id="so_tien" name="so_tien" value="{{ $thanhToanHocPhi->so_tien }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="trang_thai" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                        <select id="trang_thai" name="trang_thai" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                            <option value="chua_thanh_toan" {{ $thanhToanHocPhi->trang_thai == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                            <option value="da_thanh_toan" {{ $thanhToanHocPhi->trang_thai == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="dang_xu_ly" {{ $thanhToanHocPhi->trang_thai == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ghi_chu" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">{{ $thanhToanHocPhi->ghi_chu }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 