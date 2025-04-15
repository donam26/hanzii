@extends('layouts.dashboard')

@section('title', 'Chi tiết bài tập trắc nghiệm')
@section('page-heading', 'Chi tiết bài tập trắc nghiệm')

@php
    $active = 'ket-qua';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-medium text-gray-900">{{ $baiTapDaNop->baiTap->tieu_de ?? 'Chi tiết bài tập' }}</h3>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($baiTapDaNop->trang_thai == 'da_cham')
                        bg-green-100 text-green-800
                    @else
                        bg-blue-100 text-blue-800
                    @endif
                ">
                    {{ $baiTapDaNop->trang_thai == 'da_cham' ? 'Đã chấm' : 'Đã nộp' }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-lg font-medium text-gray-800 mb-2">Thông tin bài tập</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tên bài tập:</span>
                            <p class="text-gray-800">{{ $baiTapDaNop->baiTap->tieu_de ?? 'Không có tiêu đề' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Bài học:</span>
                            <p class="text-gray-800">{{ $baiTapDaNop->baiTap->baiHoc->tieu_de ?? 'Không có bài học' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Lớp học:</span>
                            <p class="text-gray-800">
                                @if(isset($baiTapDaNop->baiTap->baiHoc->baiHocLops))
                                    @foreach($baiTapDaNop->baiTap->baiHoc->baiHocLops as $baiHocLop)
                                        {{ $baiHocLop->lopHoc->ten ?? 'Không xác định' }}
                                        @if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    Không xác định
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Loại bài tập:</span>
                            <p class="text-gray-800">Trắc nghiệm</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Điểm tối đa:</span>
                            <p class="text-gray-800">{{ $baiTapDaNop->baiTap->diem_toi_da ?? 10 }}</p>
                        </div>
                        @if(isset($baiTapDaNop->baiTap->han_nop))
                        <div>
                            <span class="text-sm font-medium text-gray-500">Hạn nộp:</span>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($baiTapDaNop->baiTap->han_nop)->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if(isset($baiTapDaNop->baiTap->noi_dung) && !empty($baiTapDaNop->baiTap->noi_dung))
                <div class="mt-4">
                    <h5 class="text-base font-medium text-gray-800 mb-2">Yêu cầu bài tập:</h5>
                    <div class="bg-gray-50 p-4 rounded-lg prose prose-sm max-w-none">
                        {!! $baiTapDaNop->baiTap->noi_dung !!}
                    </div>
                </div>
                @endif
            </div>
            
            <div>
                <h4 class="text-lg font-medium text-gray-800 mb-2">Kết quả của bạn</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Ngày nộp:</span>
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($baiTapDaNop->ngay_nop ?? $baiTapDaNop->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Trạng thái:</span>
                            <p class="font-medium 
                                @if($baiTapDaNop->trang_thai == 'da_cham') text-green-600
                                @elseif($baiTapDaNop->trang_thai == 'da_nop') text-blue-600
                                @else text-gray-600 @endif">
                                {{ $baiTapDaNop->trang_thai == 'da_cham' ? 'Đã chấm' : 'Đã nộp' }}
                            </p>
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Số câu đúng:</span>
                            @if(isset($dapAnDung) && isset($tongSoCau) && $tongSoCau > 0)
                                <p class="text-gray-800 font-medium">
                                    <span class="text-xl {{ $dapAnDung >= $tongSoCau * 0.8 ? 'text-green-600' : ($dapAnDung >= $tongSoCau * 0.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $dapAnDung }}
                                    </span> / {{ $tongSoCau }} câu
                                </p>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                    <div class="h-2.5 rounded-full {{ $dapAnDung >= $tongSoCau * 0.8 ? 'bg-green-600' : ($dapAnDung >= $tongSoCau * 0.5 ? 'bg-yellow-500' : 'bg-red-600') }}" 
                                         style="width: {{ ($tongSoCau > 0) ? (($dapAnDung / $tongSoCau) * 100) : 0 }}%"></div>
                                </div>
                            @else
                                <p class="text-gray-600">Chưa có thông tin</p>
                            @endif
                        </div>
                        
                        <div>
                            <span class="text-sm font-medium text-gray-500">Điểm số:</span>
                            @if(isset($baiTapDaNop->diem))
                                <p class="text-2xl font-bold 
                                    {{ $baiTapDaNop->diem >= 8 ? 'text-green-600' : 
                                       ($baiTapDaNop->diem >= 6.5 ? 'text-blue-600' : 
                                        ($baiTapDaNop->diem >= 5 ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ number_format($baiTapDaNop->diem, 1) }}/{{ $baiTapDaNop->baiTap->diem_toi_da ?? 10 }}
                                </p>
                            @else
                                <p class="text-gray-600">Chưa có điểm</p>
                            @endif
                        </div>
                        
                        @if($baiTapDaNop->phan_hoi)
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nhận xét:</span>
                            <p class="text-gray-800 mt-1 p-2 bg-white rounded border border-gray-200">{{ $baiTapDaNop->phan_hoi }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="text-lg font-medium text-gray-800 mb-4">Chi tiết câu hỏi và đáp án</h4>
            
            @if(isset($cauHois) && count($cauHois) > 0)
                <div class="space-y-6">
                    @foreach($cauHois as $index => $cauHoi)
                        <div class="bg-gray-50 p-4 rounded-lg border {{ isset($cauHoi['ketqua']) && $cauHoi['ketqua'] ? 'border-green-300' : 'border-red-300' }}">
                            <div class="mb-2 flex justify-between">
                                <h5 class="font-medium text-gray-800">Câu {{ $index + 1 }}:</h5>
                                @if(isset($cauHoi['ketqua']))
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $cauHoi['ketqua'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $cauHoi['ketqua'] ? 'Đúng' : 'Sai' }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="prose prose-sm max-w-none mb-4">
                                {!! $cauHoi['noi_dung'] ?? 'Không có nội dung' !!}
                            </div>
                            
                            <div class="space-y-2 ml-2">
                                @if(isset($cauHoi['dap_an']) && is_array($cauHoi['dap_an']))
                                    @foreach($cauHoi['dap_an'] as $key => $dapAn)
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="w-5 h-5 rounded-full flex items-center justify-center mr-2
                                                    @if(isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $key)
                                                        bg-green-500 text-white
                                                    @elseif(isset($cauHoi['dap_an_chon']) && $cauHoi['dap_an_chon'] == $key)
                                                        @if(isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $key)
                                                            bg-green-500 text-white
                                                        @else
                                                            bg-red-500 text-white
                                                        @endif
                                                    @else
                                                        bg-gray-200 text-gray-500
                                                    @endif">
                                                    {{ chr(65 + $key) }}
                                                </div>
                                            </div>
                                            <div class="prose prose-sm max-w-none {{ isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $key ? 'font-medium text-green-800' : '' }}">
                                                {!! $dapAn !!}
                                                @if(isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $key)
                                                    <span class="text-green-600 ml-2">(Đáp án đúng)</span>
                                                @elseif(isset($cauHoi['dap_an_chon']) && $cauHoi['dap_an_chon'] == $key && isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] != $key)
                                                    <span class="text-red-600 ml-2">(Bạn đã chọn)</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-600 italic">Không có thông tin đáp án</p>
                                @endif
                            </div>
                            
                            @if(isset($cauHoi['giai_thich']) && !empty($cauHoi['giai_thich']))
                                <div class="mt-3 p-3 bg-blue-50 rounded">
                                    <p class="text-sm font-medium text-blue-700">Giải thích:</p>
                                    <div class="prose prose-sm max-w-none text-blue-800">
                                        {!! $cauHoi['giai_thich'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2 text-gray-600">Không tìm thấy thông tin chi tiết về các câu hỏi</p>
                    <p class="mt-1 text-sm text-gray-500">Dữ liệu bài làm có thể không đúng định dạng hoặc đã bị mất</p>
                </div>
            @endif
        </div>
        
        <div class="mt-6 text-right">
            <a href="{{ route('hoc-vien.ket-qua.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection 