@extends('layouts.dashboard')

@section('title', 'Chi tiết bài tập file')
@section('page-heading', 'Chi tiết bài tập file')

@php
    $active = 'ket-qua';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6">
        <a href="{{ route('hoc-vien.ket-qua.index') }}" class="inline-flex items-center text-sm text-red-600 hover:text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại kết quả học tập
        </a>
    </div>

    <div class="mb-6">
        <x-card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Thông tin bài tập</h3>
                    <div class="space-y-2">
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">Tên bài tập:</span>
                            <span class="font-medium">{{ $baiTap->baiTap->ten }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">Thuộc bài học:</span>
                            <span class="font-medium">{{ $baiTap->baiTap->baiHoc->ten }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">Lớp học:</span>
                            <span class="font-medium">{{ $baiTap->lopHoc->ten }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">Thời gian nộp:</span>
                            <span class="font-medium">{{ $baiTap->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-600 text-sm">Trạng thái:</span>
                            <span class="font-medium">
                                @if($baiTap->trang_thai == 'da_nop')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Đã nộp</span>
                                @elseif($baiTap->trang_thai == 'da_cham')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Đã chấm</span>
                                @elseif($baiTap->trang_thai == 'da_tra_lai')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Đã trả lại</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kết quả</h3>
                    @if($baiTap->diem !== null)
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-32 h-32 rounded-full flex items-center justify-center border-4 
                                @if($baiTap->diem >= 8) 
                                    border-green-500 text-green-600
                                @elseif($baiTap->diem >= 6.5) 
                                    border-blue-500 text-blue-600
                                @elseif($baiTap->diem >= 5) 
                                    border-yellow-500 text-yellow-600
                                @else 
                                    border-red-500 text-red-600
                                @endif">
                                <div class="text-center">
                                    <div class="text-3xl font-bold">{{ $baiTap->diem }}</div>
                                    <div class="text-sm">Điểm</div>
                                </div>
                            </div>
                        </div>
                        @if($baiTap->nhan_xet)
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-blue-700 font-medium mb-2">Nhận xét của giáo viên:</h4>
                                <p class="text-blue-800 italic">{{ $baiTap->nhan_xet }}</p>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-center">
                            <div class="p-4 bg-yellow-50 rounded-lg text-center">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium mb-2 inline-block">Chưa chấm điểm</span>
                                <p class="text-yellow-700">Bài tập của bạn đang chờ giáo viên chấm điểm</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    </div>

    <div class="mb-6">
        <x-card title="Nội dung đề bài">
            <div class="prose max-w-none">
                {!! $baiTap->baiTap->noi_dung !!}
            </div>
        </x-card>
    </div>

    <div>
        <x-card title="File bài tập đã nộp">
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @php
                                $extension = pathinfo($baiTap->ten_file, PATHINFO_EXTENSION);
                                $iconClass = match($extension) {
                                    'pdf' => 'text-red-500',
                                    'doc', 'docx' => 'text-blue-500',
                                    'xls', 'xlsx' => 'text-green-500',
                                    'ppt', 'pptx' => 'text-orange-500',
                                    'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
                                    default => 'text-gray-500'
                                };
                            @endphp

                            <div class="rounded-full bg-gray-100 p-2 {{ $iconClass }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>

                            <div>
                                <div class="font-medium">{{ $baiTap->ten_file }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($baiTap->kich_thuoc_file / 1024, 2) }} KB - {{ strtoupper($extension) }}
                                </div>
                            </div>
                        </div>

                        <a href="{{ asset('storage/' . $baiTap->duong_dan_file) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                           download>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Tải xuống
                        </a>
                    </div>
                </div>

                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mt-4">
                        <h4 class="font-medium mb-2">Xem trước:</h4>
                        <img src="{{ asset('storage/' . $baiTap->duong_dan_file) }}" alt="Xem trước" class="max-w-full h-auto rounded-lg">
                    </div>
                @endif
            </div>
        </x-card>
    </div>
@endsection 