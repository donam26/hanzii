@extends('layouts.dashboard')

@section('title', $baiHoc->ten ?? 'Chi tiết bài học')
@section('page-heading', $baiHoc->ten ?? 'Chi tiết bài học')

@php
    $active = 'lop-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Sidebar - Danh mục bài học -->
    <div class="lg:w-1/4">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-red-600 text-white px-4 py-3">
                <h3 class="font-medium">Danh sách bài học</h3>
            </div>
            <div class="p-2">
                <ul class="divide-y divide-gray-200">
                    @foreach($danhSachBaiHoc as $index => $baiHocItem)
                        @php
                            $completed = isset($tienDoBaiHocs[$baiHocItem->id]) && $tienDoBaiHocs[$baiHocItem->id]->trang_thai === 'da_hoan_thanh';
                            $current = $baiHocItem->id === $baiHoc->id;
                            $locked = !isset($tienDoBaiHocs[$baiHocItem->id]);
                        @endphp
                        <li class="relative">
                            <a href="{{ $locked ? '#' : route('hoc-vien.bai-hoc.show', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHocItem->id]) }}"
                               class="block px-4 py-3 {{ $current ? 'bg-red-50' : '' }} {{ $locked ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-50' }} relative">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($completed)
                                            <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @elseif($current)
                                            <div class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center text-white">
                                                <span class="text-xs font-medium">{{ $index + 1 }}</span>
                                            </div>
                                        @elseif($locked)
                                            <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-400 flex items-center justify-center text-white">
                                                <span class="text-xs font-medium">{{ $index + 1 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $baiHocItem->ten }}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="lg:w-3/4">
        <!-- Thông tin bài học -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $baiHoc->ten }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Thời lượng: {{ $baiHoc->thoi_luong }} phút
                        </p>
                    </div>
                    <div>
                        @if($tienDo->trang_thai !== 'da_hoan_thanh')
                            <form action="{{ route('hoc-vien.bai-hoc.cap-nhat-tien-do', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHoc->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Đánh dấu đã hoàn thành
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Đã hoàn thành
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! $baiHoc->noi_dung !!}
                </div>
            </div>
        </div>


        <!-- Danh sách bài tập -->
        @if(count($baiHoc->baiTaps) > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium text-gray-900">Bài tập</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-4">
                        @foreach($baiHoc->baiTaps as $baiTap)
                            @php
                                $daNop = isset($baiTapDaNop[$baiTap->id]);
                                $trangThai = $daNop ? $baiTapDaNop[$baiTap->id]->trang_thai : null;
                                $daDuocCham = $daNop && !is_null($baiTapDaNop[$baiTap->id]->diem);
                                $quaHan = \Carbon\Carbon::now() > \Carbon\Carbon::parse($baiTap->han_nop);
                            @endphp
                            
                            <li class="border rounded-lg overflow-hidden">
                                <div class="p-4 bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $baiTap->ten }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <span class="font-medium">Loại bài tập:</span> 
                                                @if($baiTap->loai == 'trac_nghiem')
                                                    Trắc nghiệm
                                                @elseif($baiTap->loai == 'tu_luan')
                                                    Tự luận
                                                @elseif($baiTap->loai == 'file')
                                                    Nộp file
                                                @endif
                                            </p>
                                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Hạn nộp: {{ \Carbon\Carbon::parse($baiTap->han_nop)->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($daNop && $trangThai == 'da_cham')
                                                <span class="inline-flex items-center bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Đã chấm ({{ $baiTapDaNop[$baiTap->id]->diem }}/10)
                                                </span>
                                            @elseif($daNop && $trangThai == 'da_nop')
                                                <span class="inline-flex items-center bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Đã nộp
                                                </span>
                                            @elseif($quaHan)
                                                <span class="inline-flex items-center bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Quá hạn
                                                </span>
                                            @else
                                                <span class="inline-flex items-center bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Chưa nộp
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 border-t">
                                    <div class="prose prose-sm max-w-none mb-4">
                                        {!! $baiTap->mo_ta !!}
                                    </div>
                                    <div class="mt-2">
                                        @if(!$daNop && !$quaHan)
                                            <a href="{{ route('hoc-vien.bai-hoc.form-nop-bai-tap', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHoc->id, 'baiTapId' => $baiTap->id]) }}" 
                                               class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Làm bài
                                            </a>
                                        @elseif($daNop)
                                            <a href="{{ route('hoc-vien.bai-hoc.form-nop-bai-tap', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHoc->id, 'baiTapId' => $baiTap->id]) }}" 
                                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Xem bài đã nộp
                                            </a>
                                        @else
                                            <p class="text-red-600 text-sm">Đã hết hạn nộp bài</p>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <p class="text-gray-600">Không có bài tập nào cho bài học này</p>
            </div>
        @endif
    </div>
</div>
@endsection 