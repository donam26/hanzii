@extends('layouts.dashboard')

@section('title', 'Chỉnh sửa lương')
@section('page-heading', 'Chỉnh sửa lương')

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
                    <form action="{{ route('admin.luong.update', $luong->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Thông tin lương</h3>
                                <p class="mt-1 text-sm text-gray-500">Chỉnh sửa thông tin lương cho {{ $luong->vai_tro == 'giao_vien' ? 'giáo viên' : 'trợ giảng' }} {{ $luong->nguoiDung->ho_ten }}</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phan_tram" class="block text-sm font-medium text-gray-700 mb-1">Phần trăm lương (%)</label>
                                    <input type="number" name="phan_tram" id="phan_tram" value="{{ old('phan_tram', $luong->phan_tram) }}" min="0" max="100" step="0.1" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <p class="mt-1 text-xs text-gray-500">Thông thường: Giáo viên 40%, Trợ giảng 15%</p>
                                    @error('phan_tram')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="so_tien" class="block text-sm font-medium text-gray-700 mb-1">Số tiền lương (VNĐ)</label>
                                    <input type="number" name="so_tien" id="so_tien" value="{{ old('so_tien', $luong->so_tien) }}" min="0" step="1000" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    @error('so_tien')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="ghi_chu" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                                <textarea id="ghi_chu" name="ghi_chu" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('ghi_chu', $luong->ghi_chu) }}</textarea>
                                @error('ghi_chu')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="pt-5">
                                <div class="flex justify-end">
                                    <a href="{{ route('admin.luong.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Hủy bỏ
                                    </a>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cập nhật thông tin lương
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 