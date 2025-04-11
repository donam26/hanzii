@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Đăng ký tài khoản</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Hoặc
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    đăng nhập nếu đã có tài khoản
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="ho" class="block text-sm font-medium text-gray-700">Họ</label>
                        <input id="ho" name="ho" type="text" autocomplete="family-name" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Họ" value="{{ old('ho') }}">
                        @error('ho')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="ten" class="block text-sm font-medium text-gray-700">Tên</label>
                        <input id="ten" name="ten" type="text" autocomplete="given-name" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Tên" value="{{ old('ten') }}">
                        @error('ten')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="so_dien_thoai" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input id="so_dien_thoai" name="so_dien_thoai" type="text" autocomplete="tel" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Số điện thoại" value="{{ old('so_dien_thoai') }}">
                    @error('so_dien_thoai')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Mật khẩu">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Xác nhận mật khẩu">
                </div>
                <div>
                    <label for="dia_chi" class="block text-sm font-medium text-gray-700">Địa chỉ (không bắt buộc)</label>
                    <textarea id="dia_chi" name="dia_chi"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Địa chỉ">{{ old('dia_chi') }}</textarea>
                    @error('dia_chi')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Đăng ký
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 