@extends('layouts.dashboard')

@section('title', 'Thông tin cá nhân')
@section('page-heading', 'Thông tin cá nhân')

@php
    $active = 'profile';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin cá nhân</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Thông tin chi tiết của bạn</p>
            </div>
            <a href="{{ route('hoc-vien.profile.edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Cập nhật thông tin
            </a>
        </div>

        <div class="border-t border-gray-200">
            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Ảnh đại diện</div>
                <div class="w-2/3 px-6 py-4">
                    <div class="flex items-center">
                        <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100">
                            @if($nguoiDung->avatar)
                                <img src="{{ asset('storage/' . $nguoiDung->avatar) }}" alt="Avatar" class="h-full w-full object-cover">
                            @else
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Họ và tên</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ $nguoiDung->ho_ten }}</div>
            </div>

            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Email</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ $nguoiDung->email }}</div>
            </div>

            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Số điện thoại</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ $nguoiDung->so_dien_thoai }}</div>
            </div>

            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Ngày sinh</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($hocVien->ngay_sinh)->format('d/m/Y') }}</div>
            </div>

            <div class="flex border-b border-gray-200">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Địa chỉ</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ $hocVien->dia_chi }}</div>
            </div>

            <div class="flex">
                <div class="w-1/3 px-6 py-4 bg-gray-50 text-right text-sm font-medium text-gray-500">Ngày tham gia</div>
                <div class="w-2/3 px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($nguoiDung->created_at)->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Thống kê học tập</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Tổng quan về quá trình học tập của bạn</p>
        </div>

        <div class="border-t border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 p-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tổng số khóa học</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $tongSoKhoaHoc }}</p>
                        </div>
                        <div class="bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Khóa học đang tham gia</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $dangHoc }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Khóa học đã hoàn thành</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $daHoanThanh }}</p>
                        </div>
                        <div class="bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Bảo mật</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Cài đặt bảo mật tài khoản</p>
            </div>
        </div>

        <div class="border-t border-gray-200">
            <div class="px-6 py-4">
                <a href="{{ route('hoc-vien.profile.change-password') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Đổi mật khẩu
                </a>
            </div>
        </div>
    </div>
@endsection 