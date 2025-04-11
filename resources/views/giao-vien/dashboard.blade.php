@extends('layouts.dashboard')

@section('title', 'Bảng điều khiển giáo viên')
@section('page-heading', 'Tổng quan')

@php
    $active = 'dashboard';
    $role = 'giao_vien';
@endphp

@section('content')
    <!-- Thống kê tổng quan -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Tổng số lớp học -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Lớp học đang phụ trách
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $lopHocs->count() }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('giao-vien.lop-hoc.index') }}" class="font-medium text-blue-600 hover:text-blue-900">
                            Xem tất cả
                        </a>
                    </div>
                </div>
            </div>

            <!-- Học viên -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-red-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Tổng số học viên
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $tongHocVien }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('giao-vien.hoc-vien.index') }}" class="font-medium text-red-600 hover:text-red-900">
                            Xem danh sách
                        </a>
                    </div>
                </div>
            </div>

            <!-- Yêu cầu tham gia chờ duyệt -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-yellow-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Yêu cầu chờ duyệt
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $yeuCauChoDuyet ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('giao-vien.yeu-cau-tham-gia.index', ['trang_thai' => 'cho_duyet']) }}" class="font-medium text-yellow-600 hover:text-yellow-900">
                            Xem yêu cầu
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bài tập cần chấm -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5 flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Bài tập cần chấm
                            </dt>
                            <dd>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $baiTuLuans->count() + $fileBaiTaps->count() }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('giao-vien.cham-diem.index') }}" class="font-medium text-green-600 hover:text-green-900">
                            Chấm điểm ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Lớp học đang phụ trách -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Lớp học đang phụ trách</h3>
            </div>
            <div class="p-6">
                @if ($lopHocs->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach ($lopHocs as $lopHoc)
                                <li>
                                    <div class="relative pb-8">
                                        @if (!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <a href="{{ route('giao-vien.lop-hoc.show', $lopHoc->id) }}" class="font-medium text-gray-900">
                                                            {{ $lopHoc->ten }}
                                                        </a>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="mr-2">
                                                            <i class="fas fa-book mr-1"></i> {{ $lopHoc->khoaHoc->ten }}
                                                        </span>
                                                        <span class="mr-2">
                                                            <i class="fas fa-users mr-1"></i> {{ $lopHoc->dangKyHocs()->where('trang_thai', 'da_duyet')->count() }} học viên
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-user-tie mr-1"></i> Trợ giảng: {{ $lopHoc->troGiang ? $lopHoc->troGiang->nguoiDung->ho . ' ' . $lopHoc->troGiang->nguoiDung->ten : 'Chưa phân công' }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-900">
                                                    <p class="font-medium">{{ $lopHoc->lich_hoc }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('giao-vien.lop-hoc.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                            Xem tất cả lớp học <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có lớp học nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Bạn chưa được phân công dạy lớp học nào.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bài tập cần chấm -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Bài tập cần chấm</h3>
            </div>
            <div class="p-6">
                @if ($baiTuLuans->count() > 0 || $fileBaiTaps->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach ($baiTuLuans as $baiTuLuan)
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium text-gray-900">Bài tự luận từ {{ $baiTuLuan->hocVien->nguoiDung->ho . ' ' . $baiTuLuan->hocVien->nguoiDung->ten }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="mr-2">
                                                            <i class="fas fa-book mr-1"></i> {{ $baiTuLuan->baiTap->tieu_de }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-users mr-1"></i> {{ $baiTuLuan->lopHoc->ten }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Chờ chấm điểm
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Nộp: {{ \Carbon\Carbon::parse($baiTuLuan->ngay_nop)->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            
                            @foreach ($fileBaiTaps as $fileBaiTap)
                                <li>
                                    <div class="relative pb-8">
                                        @if (!$loop->last || $baiTuLuans->count() > 0)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        <span class="font-medium text-gray-900">Bài tập file từ {{ $fileBaiTap->hocVien->nguoiDung->ho . ' ' . $fileBaiTap->hocVien->nguoiDung->ten }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="mr-2">
                                                            <i class="fas fa-book mr-1"></i> {{ $fileBaiTap->baiTap->tieu_de }}
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-users mr-1"></i> {{ $fileBaiTap->lopHoc->ten }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Chờ chấm điểm
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Nộp: {{ \Carbon\Carbon::parse($fileBaiTap->ngay_nop)->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <a href="{{ route('giao-vien.cham-diem.index', ['trang_thai' => 'da_nop']) }}" class="text-center text-sm font-medium text-green-600 hover:text-green-900">
                            Chấm bài tập <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <a href="{{ route('giao-vien.cham-diem.index') }}" class="text-center text-sm font-medium text-green-600 hover:text-green-900">
                            Xem tất cả bài tập <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có bài tập nào cần chấm</h3>
                        <p class="mt-1 text-sm text-gray-500">Tất cả các bài tập đã được chấm điểm.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 