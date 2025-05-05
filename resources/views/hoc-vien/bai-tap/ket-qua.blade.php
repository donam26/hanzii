@extends('layouts.dashboard')

@section('title', 'Kết quả bài tập')
@section('page-heading', 'Kết quả bài tập')

@php
    $active = 'lop-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-clipboard-check mr-2 text-indigo-600"></i>Kết quả bài tập: {{ $baiTapDaNop->baiTap->tieu_de }}
            </h3>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="far fa-calendar-alt mr-1"></i>
                    Ngày nộp: {{ \Carbon\Carbon::parse($baiTapDaNop->ngay_nop)->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin kết quả</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-b border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Điểm số</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if ($baiTapDaNop->diem !== null)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        {{ $baiTapDaNop->diem }} / {{ $baiTapDaNop->baiTap->diem_toi_da }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Chưa có điểm
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-b border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if ($baiTapDaNop->trang_thai == 'da_nop')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Đã nộp - Chờ chấm
                                    </span>
                                @elseif ($baiTapDaNop->trang_thai == 'da_cham')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Đã chấm
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if ($baiTapDaNop->phan_hoi)
                        <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 border-b border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Phản hồi của giáo viên</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 prose prose-sm max-w-none bg-gray-50 p-4 rounded-md">
                                {!! $baiTapDaNop->phan_hoi !!}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            
            @if ($baiTapDaNop->baiTap->loai == 'trac_nghiem')
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Chi tiết bài làm</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        @if(isset($baiTapDaNop->chiTietCauTraLois) && $baiTapDaNop->chiTietCauTraLois->count() > 0)
                            @foreach ($baiTapDaNop->chiTietCauTraLois as $index => $chiTiet)
                                <div class="px-4 py-5 sm:px-6 {{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} border-b border-gray-200">
                                    <div class="flex items-center mb-2">
                                        <span class="flex-shrink-0 h-6 w-6 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center mr-2">
                                            {{ $index + 1 }}
                                        </span>
                                        <h4 class="text-md font-medium text-gray-900">
                                            @if(isset($chiTiet->cauHoi) && isset($chiTiet->cauHoi->noi_dung))
                                                {!! $chiTiet->cauHoi->noi_dung !!}
                                            @else
                                                <span class="text-yellow-600">Không tìm thấy nội dung câu hỏi</span>
                                            @endif
                                        </h4>
                                        @if ($chiTiet->la_dap_an_dung)
                                            <span class="ml-2 h-5 w-5 text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="ml-2 h-5 w-5 text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="ml-8 mt-3">
                                        <div class="{{ $chiTiet->la_dap_an_dung ? 'text-green-600' : 'text-red-600' }} flex items-start mb-2">
                                            <span class="mr-2 flex-shrink-0">
                                                @if ($chiTiet->la_dap_an_dung)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </span>
                                            <div>
                                                <p class="text-sm font-medium">Đáp án của bạn:</p>
                                                <p class="text-sm">
                                                    @if(isset($chiTiet->dapAn) && isset($chiTiet->dapAn->noi_dung))
                                                        {!! $chiTiet->dapAn->noi_dung !!}
                                                    @else
                                                        <span class="text-yellow-600">Không tìm thấy nội dung đáp án</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        @if (!$chiTiet->la_dap_an_dung)
                                            <div class="text-green-600 flex items-start">
                                                <span class="mr-2 flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-medium">Đáp án đúng:</p>
                                                    <p class="text-sm">
                                                        @if(isset($chiTiet->cauHoi) && isset($chiTiet->cauHoi->dapAns))
                                                            @foreach ($chiTiet->cauHoi->dapAns as $dapAn)
                                                                @if ($dapAn->la_dap_an_dung)
                                                                    {!! $dapAn->noi_dung !!}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <span class="text-yellow-600">Không tìm thấy thông tin đáp án</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-5 sm:px-6 bg-yellow-50 text-yellow-700">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <p>Không có thông tin chi tiết câu trả lời.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($baiTapDaNop->baiTap->loai == 'tu_luan')
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Nội dung bài làm</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="prose prose-sm max-w-none bg-gray-50 p-4 rounded-md">
                            {!! $baiTapDaNop->noi_dung !!}
                        </div>
                    </div>
                </div>
            @elseif ($baiTapDaNop->baiTap->loai == 'file')
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">File đã nộp</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <a href="{{ asset('storage/' . $baiTapDaNop->file_path) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Tải xuống {{ $baiTapDaNop->ten_file }}
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-8 text-center">
                <a href="{{ route('hoc-vien.bai-tap.show', $baiTapDaNop->baiTap->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Quay lại bài tập
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 