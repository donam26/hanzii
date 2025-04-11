@extends('layouts.dashboard')

@section('title', 'Chi tiết bài tập tự luận')
@section('page-heading', 'Chi tiết bài tập tự luận')

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
        <x-card title="Bài làm của bạn">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="prose max-w-none">
                    {!! $baiTap->noi_dung !!}
                </div>
            </div>
        </x-card>
    </div>
@endsection 