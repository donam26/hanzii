@extends('layouts.dashboard')

@section('title', 'Đổi mật khẩu')
@section('page-heading', 'Đổi mật khẩu')

@php
    $active = 'profile';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Đổi mật khẩu</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Cập nhật mật khẩu mới</p>
        </div>
        
        <div class="border-t border-gray-200">
            <form action="{{ route('hoc-vien.profile.update-password') }}" method="POST" class="p-6">
                @csrf
                
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" id="current_password" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>
                
                <div class="flex justify-end">
                    <a href="{{ route('hoc-vien.profile.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-3">
                        Hủy
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Lưu ý về mật khẩu</h3>
        </div>
        
        <div class="border-t border-gray-200 p-6">
            <ul class="list-disc pl-5 space-y-2 text-sm text-gray-600">
                <li>Mật khẩu phải có ít nhất 8 ký tự</li>
                <li>Mật khẩu nên bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                <li>Không sử dụng mật khẩu đã dùng ở các trang web khác</li>
                <li>Không sử dụng thông tin cá nhân dễ đoán như ngày sinh, tên của bạn trong mật khẩu</li>
            </ul>
        </div>
    </div>
@endsection 